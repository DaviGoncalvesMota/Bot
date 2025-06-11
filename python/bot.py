import dearpygui.dearpygui as dpg
from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.action_chains import ActionChains
import os
import time
import requests
import clipboard

# Funções auxiliares
def verificar_login(login, senha):
    agent = {
        "User-Agent": 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36'
    }
    resposta = requests.get("http://localhost/bot_delivery/api/tela.php", params={'login': login, 'senha': senha}, headers=agent)
    return resposta.text.strip(), agent

def iniciar_bot_selenium(usuario, agent):
    dir_path = os.getcwd()
    chrome_options2 = Options()
    chrome_options2.add_argument(r"user-data-dir=" + os.path.join(dir_path, "./profile/zap"))
    driver = webdriver.Chrome(options=chrome_options2)
    driver.get("https://web.whatsapp.com/")
    time.sleep(3)

    api = requests.get("https://editacodigo.com.br/index/api-whatsapp/PJGWciGxQsM0eQi1zo0uYPgWE0KRoM8X", headers=agent)
    time.sleep(3)
    api = api.text.split(".n.")
    bolinha_notificacao = api[3].strip()
    contato_cliente = api[4].strip()
    msg_cliente = api[6].strip()

    pagina = 'http://localhost/bot_delivery/api/receber.php?'
    servidor_enviar = 'http://localhost/bot_delivery/api/enviar.php?'
    servidor_confirmar = 'http://localhost/bot_delivery/api/confirmar.php?'

    while True:
        try:
            bolinha = driver.find_elements(By.CLASS_NAME, bolinha_notificacao)
            clica_bolinha = bolinha[-1]
            ActionChains(driver).move_to_element_with_offset(clica_bolinha, 0, -20).click().perform()
            time.sleep(3)

            telefone_cliente = driver.find_element(By.XPATH, '//*[@id="main"]/header/div[2]/div/div/div/div/span')
            telefone_final = telefone_cliente.text

            todas_as_mensagens = driver.find_elements(By.CLASS_NAME, msg_cliente)
            mensagem = todas_as_mensagens[-1].text

            requests.get(pagina, params={'msg': mensagem, 'telefone': telefone_final, 'usuario': usuario}, headers=agent)
            ActionChains(driver).send_keys(Keys.ESCAPE).perform()

        except:
            try:
                api = requests.get(servidor_enviar, params={'usuario': usuario}, headers=agent).text.split(".n.")
                if api[0].strip() == "enviando":
                    id_enviar = api[1].strip()
                    contato_enviar = api[2].strip()
                    mensagem_enviar = api[3].strip()

                    caixa_de_pesquisa = driver.find_element(By.XPATH, '//*[@id="side"]/div[1]/div/div[2]/div/div/div[1]/p')
                    caixa_de_pesquisa.send_keys(contato_enviar)
                    caixa_de_pesquisa.send_keys(Keys.ENTER)

                    message_box = driver.find_element(By.XPATH, '//*[@id="main"]/footer/div[1]/div/span/div/div[2]/div[1]/div[2]/div[1]/p')
                    clipboard.copy(mensagem_enviar)
                    message_box.send_keys(Keys.CONTROL, "v")
                    message_box.send_keys(Keys.ENTER)
                    ActionChains(driver).send_keys(Keys.ESCAPE).perform()
                    requests.get(servidor_confirmar, params={'id': id_enviar}, headers=agent)
            except:
                print('Aguardando novas mensagens...')

# Telas com Dear PyGui
def login_screen():
    with dpg.handler_registry():
        dpg.add_key_press_handler(callback=on_key_press)

    with dpg.window(label="Bot Delivery - Login", tag="login_window", width=400, height=250, pos=(300, 150)):
        dpg.add_text("Email:")
        login_input = dpg.add_input_text(tag="login_input", hint="Digite seu email")
        dpg.add_text("Senha:")
        senha_input = dpg.add_input_text(tag="senha_input", password=True, hint="Digite sua senha")
        dpg.add_button(label="Entrar", callback=lambda: entrar(login_input, senha_input))
        dpg.add_text("", tag="login_erro")

def entrar(login_input, senha_input):
    login = dpg.get_value(login_input)
    senha = dpg.get_value(senha_input)
    resposta, agent = verificar_login(login, senha)
    if resposta == '1':
        dpg.delete_item("login_window")
        inicio_screen(login, agent)
    else:
        dpg.set_value("login_erro", "Erro no login, tente novamente")
        dpg.configure_item("login_erro", color=(255, 0, 0))

def inicio_screen(usuario, agent):
    with dpg.window(label="Bot Delivery - Início", tag="inicio_window", width=400, height=250, pos=(300, 150)):
        dpg.add_text(f"Bem-vindo {usuario}, ao Bot de Delivery de Pizzas!")
        dpg.add_text("Tenha o celular em mãos e clique abaixo para iniciar o bot.")
        dpg.add_button(label="Iniciar Bot", callback=lambda: iniciar_bot_callback(usuario, agent))
        dpg.add_text("", tag="label_status")

def iniciar_bot_callback(usuario, agent):
    dpg.set_value("label_status", f"Iniciando bot para o usuário {usuario}...")
    iniciar_bot_selenium(usuario, agent)

def on_key_press(sender, app_data):
    if app_data == 27:  # Esc key
        dpg.delete_item("login_window")

def main():
    dpg.create_context()
    login_screen()
    dpg.create_viewport(title='Bot Delivery', width=800, height=600)
    dpg.setup_dearpygui()
    dpg.show_viewport()
    dpg.start_dearpygui()
    dpg.destroy_context()

if __name__ == "__main__":
    main()

