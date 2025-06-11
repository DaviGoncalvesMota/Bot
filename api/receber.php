<?php

require_once '../php/conn.php';

$numero_get = $_GET['telefone'];
$usuario_get = $_GET['usuario'];
$msg_get = $_GET['msg'];

// Funções

// Data e Hora.
date_default_timezone_set(timezoneId: 'America/Sao_Paulo'); // Define o fuso horário
// Obtém o timestamp atual
$now = time();

// Formata a data e hora
$data_hora = date(format: "Y-m-d H:i:s", timestamp: $now);
// echo $data_hora;

// function pata identificar numeros
function ehNumero($texto): bool
{
    return is_numeric(value: $texto);
}

// function para letra maiuscula
function letraMaiuscula($texto): string
{
    // Converte a primeira letra para maiúscula
    $primeira_letra = mb_strtoupper(string: mb_substr(string: $texto, start: 0, length: 1));
    // Pega o resto da palavra
    $resto_palavra = mb_substr(string: $texto, start: 1);
    return "{$primeira_letra}{$resto_palavra}";
}

function insertEnvios($telefone, $mensagem, $status, $usuario): void
{
    global $conn;

    $inserirEnvio = "INSERT INTO envios (telefone, mensagem, status, usuario) VALUES ('$telefone', '$mensagem', '$status', '$usuario')";
    $query = mysqli_query(mysql: $conn, query: $inserirEnvio);

    if (!$query) {
        echo "Erro ao inserir o envio: " . mysqli_error(mysql: $conn);
    } else {
        return;
    }
}

function insertPedidos($id_cliente, $nome, $email_painel, $telefone, $endereco, $status, $data_hora)
{
    global $conn;

    $inserirPedidos = "INSERT INTO pedidos 
    (id_cliente, nome, email_painel, telefone, endereco, status, data_hora) 
                        VALUES 
    ('$id_cliente', '$nome', '$email_painel', '$telefone', '$endereco', '$status', '$data_hora')";

    $query = mysqli_query(mysql: $conn, query: $inserirPedidos);

    if (!$query) {
        echo "Erro ao inserir o envio: " . mysqli_error(mysql: $conn);
    } else {
        return;
    }
}

function updateSituacao($telefone, $situacao, $email_painel)
{
    global $conn;

    $atualizaSituacao = "UPDATE clientes SET situacao = '$situacao' WHERE email_painel = '$email_painel' AND telefone = '$telefone'";
    $query = mysqli_query(mysql: $conn, query: $atualizaSituacao);

    if (!$query) {
        echo "Erro ao atualizar situação: " . mysqli_error(mysql: $conn);
    } else {
        return;
    }
}

function defUpdatePedidos($set, $msg_get, $email_painel, $numero_get, $status)
{
    global $conn;

    $defQtd = "UPDATE pedidos SET $set = '$msg_get' WHERE email_painel = '$email_painel' AND telefone = '$numero_get' AND status = '$status'";
    $query = mysqli_query(mysql: $conn, query: $defQtd);

    if (!$query) {
        echo "Erro ao atualizar situação: " . mysqli_error(mysql: $conn);
    } else {
        return;
    }
}

function selectProdutos($email_painel)
{
    global $conn;

    $buscar_produtos = "SELECT * FROM produtos WHERE email_painel = '$email_painel'";
    $resultado_busca_produtos = mysqli_query(mysql: $conn, query: $buscar_produtos);

    while ($dados_produtos = mysqli_fetch_array($resultado_busca_produtos)) {
        $nome_produto = $dados_produtos['nome'];
        $num_produto = $dados_produtos['numero_produto'];
        $preco_produto = number_format($dados_produtos['preco'], 2, ',', '.');
        $msg .= "($num_produto) *$nome_produto* - R$ $preco_produto\n";
    }

    if (!$resultado_busca_produtos) {
        echo "Erro ao buscar produtos: " . mysqli_error(mysql: $conn);
    } else {
        return $msg;
    }
}

// Buscar Cliente
$busca_cliente = "SELECT * FROM clientes WHERE telefone = '$numero_get' AND email_painel = '$usuario_get'";
$cliente = mysqli_query(mysql: $conn, query: $busca_cliente);
$total_clientes = mysqli_num_rows(result: $cliente);

while ($dados_cliente = mysqli_fetch_array(result: $cliente)) {
    $id_cliente = $dados_cliente['id'];
    $nome_cliente = $dados_cliente['nome'];
    $email_painel = $dados_cliente['email_painel'];
    $telefone_cliente = $dados_cliente['telefone'];
    $endereco_cliente = $dados_cliente['endereco'];
    $situacao_cliente = $dados_cliente['situacao'];
}

// Buscar Login
$busca_painel = "SELECT * FROM login WHERE email = '$usuario_get'";
$usuario_painel = mysqli_query(mysql: $conn, query: $busca_painel);

while ($dados_painel = mysqli_fetch_array(result: $usuario_painel)) {
    $id_painel = $dados_painel['id'];
    $email_painel = $dados_painel['email'];
    $senha_painel = $dados_painel['senha'];
    $nome_painel = $dados_painel['nome'];
    $dinheiro_painel = $dados_painel['dinheiro'];
    $pix_painel = $dados_painel['pix'];
    $cartao_painel = $dados_painel['cartao'];
    $caderneta_painel = $dados_painel['caderneta'];
    $tipo_painel = $dados_painel['tipo'];
    $status_painel = $dados_painel['status'];
}

// Busca Produtos
$busca_produtos = "SELECT * FROM produtos WHERE email_painel = '$usuario_get'";
$resultado_produtos = mysqli_query(mysql: $conn, query: $busca_produtos);

while ($dados_produtos = mysqli_fetch_array(result: $resultado_produtos)) {
    $id_produto = $dados_produtos['id'];
    $nome_produto = $dados_produtos['nome'];
    $preco_produto = $dados_produtos['preco'];
    $email_painel = $dados_produtos['email_painel'];
}


// Insere usuario novo
if ($total_clientes == 0) {
    $sql = "INSERT INTO clientes (telefone, email_painel) VALUES ('$numero_get', '$usuario_get')";
    $query = mysqli_query(mysql: $conn, query: $sql);

    if ($query) {
        $msg = "Para começar, me diga seu nome 😊";
        insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
    }
}

// Preenchendo o nome do usuario
if ($total_clientes == 1 && $nome_cliente == "") {
    $msg_usuario = letraMaiuscula(texto: $msg_get);

    $update_name = "UPDATE clientes SET nome = '$msg_usuario' WHERE email_painel = '$email_painel' AND telefone = '$numero_get'";
    $query = mysqli_query($conn, $update_name);

    if ($query) {
        $msg .= "⭐Bem vindo, *$msg_usuario*, ao nosso delivery ⭐!\n\n";
        $msg .= "Estamos felizes em tê-lo conosco!\n";
        $msg .= "Aqui estão os produtos disponíveis,\njuntamente com seus respectivos preços:\n\n";

        $msg .= selectProdutos(email_painel: $usuario_get);

        $msg .= "\nPara selecionar seu produto, basta\n";
        $msg .= "enviar o número correspondente a ele.\n";
        $msg .= "Estamos prontos para atendê-lo!";

        insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
    }
}


if ($total_clientes == 1 && $nome_cliente && $situacao_cliente == "") {
    if (ehNumero(texto: $msg_get)) {

        $get_num_produto = "SELECT * FROM produtos WHERE email_painel = '$email_painel' AND numero_produto = '$msg_get'";
        $query = mysqli_query(mysql: $conn, query: $get_num_produto);

        while ($dados_produtos = mysqli_fetch_array(result: $query)) {
            $nome_produto = $dados_produtos['nome'];
        }

        if ($query) {
            insertPedidos(
                id_cliente: $id_cliente, 
                nome: $nome_cliente, 
                email_painel: $email_painel, 
                telefone: $numero_get,
                endereco: $endereco, 
                status: $nome_produto, 
                data_hora: $data_hora
            );

            $msg = "❗ *Envie apenas números!*

Quantas(os) $nome_produto
você gostaria de pedir?
                            
É só digitar: *1*, *2* ou *3*,  
conforme a quantidade desejada.

caso queira a descrição completa do item, digite *desc*
                ";

            insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

            updateSituacao(telefone: $numero_get, situacao: 'compra_produto', email_painel: $email_painel);

        }
    } else {
        $msg .= "❗ *Envie apenas números!*\n\n";
        $msg .= "Aqui estão os produtos disponíveis,\njuntamente com seus respectivos preços:\n\n";

        $msg .= selectProdutos(email_painel: $usuario_get);

        $msg .= "\nPara selecionar seu produto, basta\n";
        $msg .= "enviar o número correspondente a ele.\n";
        $msg .= "Estamos prontos para atendê-lo!";

        insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
    }
}

// Pepperoni
if ($situacao_cliente == "compra_produto" && $nome_cliente) {
    if (ehNumero(texto: $msg_get)) {
        defUpdatePedidos(set: 'qtd_produtos', msg_get: $msg_get, email_painel: $email_painel, numero_get: $numero_get, status: $nome_produto);

        if ($query) {
            $busca_pedidos = "SELECT * FROM pedidos WHERE email_painel = '$usuario_get' AND telefone = '$numero_get' AND status = 'compra_produto'";
            $pedidos = mysqli_query(mysql: $conn, query: $busca_pedidos);

            while ($dados_pedidos = mysqli_fetch_array(result: $pedidos)) {
                $id_pedido = $dados_pedidos['id'];
                $nome_pedido = $dados_pedidos['nome'];
                $telefone_pedido = $dados_pedidos['telefone'];
                $endereco_pedido = $dados_pedidos['endereco'];
                $forma_pagamento = $dados_pedidos['pagamento'];
                $status = $dados_pedidos['status'];
                $qtd_produtos = $dados_pedidos['qtd_produtos'];
                $data_hora_pedido = $dados_pedidos['data_hora'];
                $email_painel = $dados_pedidos['email_painel'];
            }
 
            $hora = date("H:i", strtotime($data_hora_pedido));
            $data = date("d/m/Y", strtotime($data_hora_pedido));

            if ($qtd_produtos > 0) {
                $pedido = "*$qtd_produtos* - $status";
            }

            $total_pepperoni_p = $qtd_pepperoni_p * $prod_pepperoni;
            $total_frango_p = $qtd_frango_p * $prod_frango;
            $total_quatroqueijos_p = $qtd_quatroqueijos_p * $prod_quatroqueijos;
            $total_brigadeiro_p = $qtd_brigadeiro_p * $prod_brigadeiro;

            $total_geral_p = $total_pepperoni_p + $total_frango_p + $total_quatroqueijos_p + $total_brigadeiro_p;

            $msg = "📋Nota Fiscal - Pedido de Pizzas🍕
📅 Data: $data_p
🕒 Hora: $hora_p
            
🛒 Pedido: 
$pedido1_p
$pedido2_p
$pedido3_p
$pedido4_p
            
💸 Total: R$ $total_geral_p
    ";

            insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

            if ($query) {
                updateSituacao(telefone: $numero_get, situacao: 'inicio_compra', email_painel: $email_painel);

                $atualizaPedidosPepperoni = "UPDATE pedidos SET status = 'inicio_compra' WHERE email_painel = '$usuario_get' AND status = 'compra_pepperoni'";
                $query = mysqli_query(mysql: $conn, query: $atualizaPedidosPepperoni);

                if ($query) {
                    $msg = "*Escolha outro sabor de pizza ou encerre a compra*:

    🍕(1) *Pepperoni* - R$ $prod_pepperoni
    🍕(2) *Frango* - R$ $prod_frango
    🍕(3) *Quatro Queijos* - R$ $prod_quatroqueijos
    🍕(4) *Brigadeiro* - R$ $prod_brigadeiro
    🛒(5) *Finalizar Compra*
        
Para selecionar sua pizza, basta
enviar o número correspondente a
ela. Estamos prontos para atende-lo!";

                    insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
                }
            }
        }
    } else {
        $msg = "⚠️ *Ops! Envie apenas números, por favor!*

Quantas pizzas de *Pepperoni* 🍕 você deseja?

Digite um número como: *1*, *2* ou *3*  
de acordo com a quantidade que quer pedir.  
Estamos preparando tudo com carinho! 

";
        insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);


    }
}

// Frango
if ($situacao_cliente == "compra_frango" && $nome_cliente) {
    if (ehNumero(texto: $msg_get)) {
        defUpdatePedidos(set: 'qtd_frango', msg_get: $msg_get, email_painel: $email_painel, numero_get: $numero_get, status: $status = 'compra_pepperoni');

        if ($query) {
            $busca_pedidos_frango = "SELECT * FROM pedidos WHERE email_painel = '$usuario_get' AND telefone = '$numero_get' AND status = 'compra_frango'";
            $pedidos_frango = mysqli_query(mysql: $conn, query: $busca_pedidos_frango);

            while ($dados_pedidos = mysqli_fetch_array(result: $pedidos_frango)) {
                $id_pedido_f = $dados_pedidos['id'];
                $nome_pedido_f = $dados_pedidos['nome'];
                $telefone_pedido_f = $dados_pedidos['telefone'];
                $endereco_pedido_f = $dados_pedidos['endereco'];
                $qtd_pepperoni_f = $dados_pedidos['qtd_pepperoni'];
                $qtd_frango_f = $dados_pedidos['qtd_frango'];
                $qtd_quatroqueijos_f = $dados_pedidos['qtd_quatroqueijos'];
                $qtd_brigadeiro_f = $dados_pedidos['qtd_brigadeiro'];
                $forma_pagamento_f = $dados_pedidos['pagamento'];
                $status_f = $dados_pedidos['status'];
                $data_hora_pedido_f = $dados_pedidos['data_hora'];
                $email_painel_f = $dados_pedidos['email_painel'];
            }

            $hora_f = date("H:i", strtotime($data_hora_pedido_f));
            $data_f = date("d/m/Y", strtotime($data_hora_pedido_f));


            if ($qtd_pepperoni_f > 0) {
                $pedido1_f = "*$qtd_pepperoni_f* - Pepperoni 🍕";
            }
            if ($qtd_frango_f > 0) {
                $pedido2_f = "*$qtd_frango_f* - Frango 🍕";
            }
            if ($qtd_quatroqueijos_f > 0) {
                $pedido3_f = "*$qtd_quatroqueijos_f* - Quatro Queijos 🍕";
            }
            if ($qtd_brigadeiro_f > 0) {
                $pedido4_f = "*$qtd_brigadeiro_f* - Brigadeiro 🍕";
            }

            $total_pepperoni_f = $qtd_pepperoni_f * $prod_pepperoni;
            $total_frango_f = $qtd_frango_f * $prod_frango;
            $total_quatroqueijos_f = $qtd_quatroqueijos_f * $prod_quatroqueijos;
            $total_brigadeiro_f = $qtd_brigadeiro_f * $prod_brigadeiro;

            $total_geral_f = $total_pepperoni_f + $total_frango_f + $total_quatroqueijos_f + $total_brigadeiro_f;

            $msg = "📋Nota Fiscal - Pedido de Pizzas🍕
📅 Data: $data_f
🕒 Hora: $hora_f
            
🛒 Pedido: 
$pedido1_f
$pedido2_f
$pedido3_f
$pedido4_f
            
💸 Total: R$ $total_geral_f
    ";

            insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

            if ($query) {
                updateSituacao(telefone: $numero_get, situacao: 'inicio_compra', email_painel: $email_painel);

                $atualizaPedidosFrango = "UPDATE pedidos SET status = 'inicio_compra' WHERE email_painel = '$usuario_get' AND status = 'compra_frango'";
                $query = mysqli_query(mysql: $conn, query: $atualizaPedidosFrango);

                if ($query) {
                    $msg = "*Escolha outro sabor de pizza ou encerre a compra*:

    🍕(1) *Pepperoni* - R$ $prod_pepperoni
    🍕(2) *Frango* - R$ $prod_frango
    🍕(3) *Quatro Queijos* - R$ $prod_quatroqueijos
    🍕(4) *Brigadeiro* - R$ $prod_brigadeiro
    🛒(5) *Finalizar Compra*
        
Para selecionar sua pizza, basta
enviar o número correspondente a
ela. Estamos prontos para atende-lo!";

                    insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
                }
            }
        }
    } else {
        $msg = "⚠️ *Ops! Envie apenas números, por favor!*

Quantas pizzas de *Pepperoni* 🍕 você deseja?

Digite um número como: *1*, *2* ou *3*  
de acordo com a quantidade que quer pedir.  
Estamos preparando tudo com carinho! ";

        insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
    }
}

// QuatroQueijos
if ($situacao_cliente == "compra_quatroqueijos" && $nome_cliente) {
    if (ehNumero(texto: $msg_get)) {
        defUpdatePedidos(set: 'qtd_quatroqueijos', msg_get: $msg_get, email_painel: $email_painel, numero_get: $numero_get, status: $status = 'compra_pepperoni');

        if ($query) {
            $busca_pedidos_quatroqueijos = "SELECT * FROM pedidos WHERE email_painel = '$usuario_get' AND telefone = '$numero_get' AND status = 'compra_quatroqueijos'";
            $pedidos_quatroqueijos = mysqli_query(mysql: $conn, query: $busca_pedidos_quatroqueijos);

            while ($dados_pedidos = mysqli_fetch_array(result: $pedidos_quatroqueijos)) {
                $id_pedido_q = $dados_pedidos['id'];
                $nome_pedido_q = $dados_pedidos['nome'];
                $telefone_pedido_q = $dados_pedidos['telefone'];
                $endereco_pedido_q = $dados_pedidos['endereco'];
                $qtd_pepperoni_q = $dados_pedidos['qtd_pepperoni'];
                $qtd_frango_q = $dados_pedidos['qtd_frango'];
                $qtd_quatroqueijos_q = $dados_pedidos['qtd_quatroqueijos'];
                $qtd_brigadeiro_q = $dados_pedidos['qtd_brigadeiro'];
                $forma_pagamento_q = $dados_pedidos['pagamento'];
                $status_q = $dados_pedidos['status'];
                $data_hora_pedido_q = $dados_pedidos['data_hora'];
                $email_painel_q = $dados_pedidos['email_painel'];
            }

            $hora_q = date("H:i", strtotime($data_hora_pedido_q));
            $data_q = date("d/m/Y", strtotime($data_hora_pedido_q));


            if ($qtd_pepperoni_q > 0) {
                $pedido1_q = "*$qtd_pepperoni_q* - Pepperoni 🍕";
            }
            if ($qtd_frango_q > 0) {
                $pedido2_q = "*$qtd_frango_q* - Frango 🍕";
            }
            if ($qtd_quatroqueijos_q > 0) {
                $pedido3_q = "*$qtd_quatroqueijos_q* - Quatro Queijos 🍕";
            }
            if ($qtd_brigadeiro_q > 0) {
                $pedido4_q = "*$qtd_brigadeiro_q* - Brigadeiro 🍕";
            }

            $total_pepperoni_q = $qtd_pepperoni_q * $prod_pepperoni;
            $total_frango_q = $qtd_frango_q * $prod_frango;
            $total_quatroqueijos_q = $qtd_quatroqueijos_q * $prod_quatroqueijos;
            $total_brigadeiro_q = $qtd_brigadeiro_q * $prod_brigadeiro;

            $total_geral_q = $total_pepperoni_q + $total_frango_q + $total_quatroqueijos_q + $total_brigadeiro_q;

            $msg = "📋Nota Fiscal - Pedido de Pizzas🍕
📅 Data: $data_q
🕒 Hora: $hora_q
            
🛒 Pedido: 
$pedido1_q
$pedido2_q
$pedido3_q
$pedido4_q
            
💸 Total: R$ $total_geral_q
    ";

            insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

            if ($query) {
                updateSituacao(telefone: $numero_get, situacao: 'inicio_compra', email_painel: $email_painel);

                $atualizaPedidosQuatroQueijos = "UPDATE pedidos SET status = 'inicio_compra' WHERE email_painel = '$usuario_get' AND status = 'compra_quatroqueijos'";
                $query = mysqli_query(mysql: $conn, query: $atualizaPedidosQuatroQueijos);

                if ($query) {
                    $msg = "*Escolha outro sabor de pizza ou encerre a compra*:

    🍕(1) *Pepperoni* - R$ $prod_pepperoni
    🍕(2) *Frango* - R$ $prod_frango
    🍕(3) *Quatro Queijos* - R$ $prod_quatroqueijos
    🍕(4) *Brigadeiro* - R$ $prod_brigadeiro
    🛒(5) *Finalizar Compra*
        
Para selecionar sua pizza, basta
enviar o número correspondente a
ela. Estamos prontos para atende-lo!";

                    insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
                }
            }
        }
    } else {
        $msg = "⚠️ *Ops! Envie apenas números, por favor!*

Quantas pizzas de *Quatro Queijos* 🍕 você deseja?

Digite um número como: *1*, *2* ou *3*  
de acordo com a quantidade que quer pedir.  
Estamos preparando tudo com carinho! ";

        insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
    }
}


// Brigadeiro
if ($situacao_cliente == "compra_brigadeiro" && $nome_cliente) {
    if (ehNumero(texto: $msg_get)) {
        defUpdatePedidos(set: 'qtd_brigadeiro', msg_get: $msg_get, email_painel: $email_painel, numero_get: $numero_get, status: $status = 'compra_pepperoni');

        if ($query) {
            $busca_pedidos_brigadeiro = "SELECT * FROM pedidos WHERE email_painel = '$usuario_get' AND telefone = '$numero_get' AND status = 'compra_brigadeiro'";
            $pedidos_brigadeiro = mysqli_query(mysql: $conn, query: $busca_pedidos_brigadeiro);

            while ($dados_pedidos = mysqli_fetch_array(result: $pedidos_brigadeiro)) {
                $id_pedido_b = $dados_pedidos['id'];
                $nome_pedido_b = $dados_pedidos['nome'];
                $telefone_pedido_b = $dados_pedidos['telefone'];
                $endereco_pedido_b = $dados_pedidos['endereco'];
                $qtd_pepperoni_b = $dados_pedidos['qtd_pepperoni'];
                $qtd_frango_b = $dados_pedidos['qtd_frango'];
                $qtd_quatroqueijos_b = $dados_pedidos['qtd_quatroqueijos'];
                $qtd_brigadeiro_b = $dados_pedidos['qtd_brigadeiro'];
                $forma_pagamento_b = $dados_pedidos['pagamento'];
                $status_b = $dados_pedidos['status'];
                $data_hora_pedido_b = $dados_pedidos['data_hora'];
                $email_painel_b = $dados_pedidos['email_painel'];
            }

            $hora_b = date("H:i", strtotime($data_hora_pedido_b));
            $data_b = date("d/m/Y", strtotime($data_hora_pedido_b));


            if ($qtd_pepperoni_b > 0) {
                $pedido1_b = "*$qtd_pepperoni_b* - Pepperoni 🍕";
            }
            if ($qtd_frango_b > 0) {
                $pedido2_b = "*$qtd_frango_b* - Frango 🍕";
            }
            if ($qtd_quatroqueijos_b > 0) {
                $pedido3_b = "*$qtd_quatroqueijos_b* - Quatro Queijos 🍕";
            }
            if ($qtd_brigadeiro_b > 0) {
                $pedido4_b = "*$qtd_brigadeiro_b* - Brigadeiro 🍕";
            }

            $total_pepperoni_b = $qtd_pepperoni_b * $prod_pepperoni;
            $total_frango_b = $qtd_frango_b * $prod_frango;
            $total_quatroqueijos_b = $qtd_quatroqueijos_b * $prod_quatroqueijos;
            $total_brigadeiro_b = $qtd_brigadeiro_b * $prod_brigadeiro;

            $total_geral_b = $total_pepperoni_b + $total_frango_b + $total_quatroqueijos_b + $total_brigadeiro_b;

            $msg = "📋Nota Fiscal - Pedido de Pizzas🍕
📅 Data: $data_b
🕒 Hora: $hora_b
            
🛒 Pedido: 
$pedido1_b
$pedido2_b
$pedido3_b
$pedido4_b
            
💸 Total: R$ $total_geral_b
    ";

            insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

            if ($query) {
                updateSituacao(telefone: $numero_get, situacao: 'inicio_compra', email_painel: $email_painel);

                $atualizaPedidosBrigadeiro = "UPDATE pedidos SET status = 'inicio_compra' WHERE email_painel = '$usuario_get' AND status = 'compra_brigadeiro'";
                $query = mysqli_query(mysql: $conn, query: $atualizaPedidosBrigadeiro);

                if ($query) {
                    $msg = "*Escolha outro sabor de pizza ou encerre a compra*:

    🍕(1) *Pepperoni* - R$ $prod_pepperoni
    🍕(2) *Frango* - R$ $prod_frango
    🍕(3) *Quatro Queijos* - R$ $prod_quatroqueijos
    🍕(4) *Brigadeiro* - R$ $prod_brigadeiro
    🛒(5) *Finalizar Compra*
        
Para selecionar sua pizza, basta
enviar o número correspondente a
ela. Estamos prontos para atende-lo!";

                    insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
                }
            }
        }
    } else {
        $msg = "⚠️ *Ops! Envie apenas números, por favor!*

Quantas pizzas de *Brigadeiro* 🍕 você deseja?

Digite um número como: *1*, *2* ou *3*  
de acordo com a quantidade que quer pedir.  
Estamos preparando tudo com carinho! ";

        insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
    }
}

if ($situacao_cliente == 'inicio_compra' && $nome_cliente) {
    if (ehNumero(texto: $msg_get)) {
        if ($msg_get == "1") {
            updateSituacao(telefone: $numero_get, situacao: 'compra_pepperoni', email_painel: $email_painel);

            defUpdatePedidos(set: 'status', msg_get: 'compra_pepperoni', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'inicio_compra');

            if ($query) {
                $msg = "Quantas pizzas de *Pepperoni* 🍕 você deseja?
                
Digite um número como: *1*, *2* ou *3*  
de acordo com a quantidade que quer pedir.  

Estamos preparando tudo com carinho! 😊                    
Obrigado!";

                insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
            }

        }
        if ($msg_get == "2") {
            updateSituacao(telefone: $numero_get, situacao: 'compra_frango', email_painel: $email_painel);

            defUpdatePedidos(set: 'status', msg_get: 'compra_frango', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'inicio_compra');

            $msg = "Quantas pizzas de *Frango* 🍕 você deseja?
                
Digite um número como: *1*, *2* ou *3*  
de acordo com a quantidade que quer pedir.  

Estamos preparando tudo com carinho! 😊     
Obrigado!";


            insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

        }
        if ($msg_get == "3") {

            updateSituacao(telefone: $numero_get, situacao: 'compra_quatroqueijos', email_painel: $email_painel);

            defUpdatePedidos(set: 'status', msg_get: 'compra_quatroqueijos', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'inicio_compra');

            $msg = "Quantas pizzas de *Quatro Queijos* 🍕 você deseja?
                
Digite um número como: *1*, *2* ou *3*  
de acordo com a quantidade que quer pedir.  

Estamos preparando tudo com carinho! 😊
Obrigado!";

            insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

        }
        if ($msg_get == "4") {

            updateSituacao(telefone: $numero_get, situacao: 'compra_brigadeiro', email_painel: $email_painel);

            defUpdatePedidos(set: 'status', msg_get: 'compra_brigadeiro', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'inicio_compra');

            $msg = "Quantas pizzas de *Brigadeiro* 🍕 você deseja?
                
Digite um número como: *1*, *2* ou *3*  
de acordo com a quantidade que quer pedir.  
            
Estamos preparando tudo com carinho! 😊
Obrigado!";

            insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

        }
        if ($msg_get == "5") {
            defUpdatePedidos(set: 'status', msg_get: 'pagamento', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'inicio_compra');
            updateSituacao(telefone: $numero_get, situacao: 'pagamento', email_painel: $email_painel);

            if ($query) {
                if ($dinheiro_painel > 0) {
                    $din = "*(1)* Dinheiro 💸";
                }
                if ($pix_painel > 0) {
                    $pix = "*(2)* Pix 📲";
                }
                if ($cartao_painel > 0) {
                    $cartao = "*(3)* Cartão de Crédito 💳";
                }
                if ($caderneta_painel > 0) {
                    $caderneta = "*(4)* Caderneta 📖";
                }

                $msg = "Escolha a forma de pagamento:

$din
$pix
$cartao
$caderneta";

                insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
            }
        }
    } else {
        $msg = "*Envie apenas números por favor*:

    🍕(1) *Pepperoni* - R$ $prod_pepperoni
    🍕(2) *Frango* - R$ $prod_frango
    🍕(3) *Quatro Queijos* - R$ $prod_quatroqueijos
    🍕(4) *Brigadeiro* - R$ $prod_brigadeiro
    🛒(5) *Finalizar Compra*
        
Para selecionar sua pizza, basta
enviar o número correspondente a
ela. Estamos prontos para atende-lo!";


    }
}

if ($situacao_cliente == "pagamento" && $nome_cliente) {
    if (ehNumero(texto: $msg_get)) {

        if ($msg_get == "1") {
            defUpdatePedidos(set: 'pagamento', msg_get: 'dinheiro', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'pagamento');


            if ($endereco_cliente == "") {
                $msg = "Poderia me passar seu endereço completo 📍 
para que possamos enviar seu pedido? 🛵 🍕";

                insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

                updateSituacao(telefone: $numero_get, situacao: 'cadastrar_endereco', email_painel: $email_painel);

                defUpdatePedidos(set: 'status', msg_get: 'cadastrar_endereco', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'pagamento');
            }
            if ($endereco_cliente) {
                $msg = "O endereço da entrega é esse?

🛵 Entrega: *$endereco_cliente*

*(1)* SIM
*(2)* NÃO
                    ";

                insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

                updateSituacao(telefone: $numero_get, situacao: 'confirmar_endereco', email_painel: $email_painel);

                defUpdatePedidos(set: 'status', msg_get: 'confirmar_endereco', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'pagamento');

            }
        }

        if ($msg_get == "2") {
            defUpdatePedidos(set: 'pagamento', msg_get: 'pix', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'pagamento');


            if ($endereco_cliente == "") {
                $msg = "Poderia me passar seu endereço completo 📍 
para que possamos enviar seu pedido? 🛵 🍕";

                insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

                updateSituacao(telefone: $numero_get, situacao: 'cadastrar_endereco', email_painel: $email_painel);

                defUpdatePedidos(set: 'status', msg_get: 'cadastrar_endereco', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'pagamento');
            }
            if ($endereco_cliente) {
                $msg = "O endereço da entrega é esse?

🛵 Entrega: *$endereco_cliente*

*(1)* SIM
*(2)* NÃO
                ";

                insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

                updateSituacao(telefone: $numero_get, situacao: 'confirmar_endereco', email_painel: $email_painel);

                defUpdatePedidos(set: 'status', msg_get: 'confirmar_endereco', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'pagamento');
            }
        }

        if ($msg_get == "3") {
            defUpdatePedidos(set: 'pagamento', msg_get: 'cartao', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'pagamento');

            if ($query) {
                if ($endereco_cliente == "") {
                    $msg = "Poderia me passar seu endereço completo 📍 
para que possamos enviar seu pedido? 🛵 🍕";

                    insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

                    updateSituacao(telefone: $numero_get, situacao: 'cadastrar_endereco', email_painel: $email_painel);

                    defUpdatePedidos(set: 'status', msg_get: 'cadastrar_endereco', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'pagamento');
                }
                if ($endereco_cliente) {
                    $msg = "O endereço da entrega é esse?

🛵 Entrega: *$endereco_cliente*

*(1)* SIM
*(2)* NÃO
                    ";

                    insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

                    if ($query) {
                        updateSituacao(telefone: $numero_get, situacao: 'confirmar_endereco', email_painel: $email_painel);

                        defUpdatePedidos(set: 'status', msg_get: 'confirmar_endereco', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'pagamento');
                    }
                }
            }
        }

        if ($msg_get == "4") {
            defUpdatePedidos(set: 'pagamento', msg_get: 'caderneta', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'pagamento');


            if ($endereco_cliente == "") {
                $msg = "Poderia me passar seu endereço completo 📍
para que possamos enviar seu pedido? 🛵 🍕";

                insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

                updateSituacao(telefone: $numero_get, situacao: 'cadastrar_endereco', email_painel: $email_painel);

                defUpdatePedidos(set: 'status', msg_get: 'cadastrar_endereco', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'pagamento');
            }
            if ($endereco_cliente) {
                $msg = "O endereço da entrega é esse?

🛵 Entrega: *$endereco_cliente*

*(1)* SIM
*(2)* NÃO
                ";

                insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

                updateSituacao(telefone: $numero_get, situacao: 'confirmar_endereco', email_painel: $email_painel);

                defUpdatePedidos(set: 'status', msg_get: 'confirmar_endereco', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'pagamento');
            }
        }
    }
}

if ($situacao_cliente == "cadastrar_endereco" && $nome_cliente) {
    $novoEnderecoCliente = "UPDATE clientes SET endereco = '$msg_get' WHERE email_painel = '$usuario_get' AND telefone = '$numero_get'";
    $query = mysqli_query(mysql: $conn, query: $novoEnderecoCliente);

    $novoEnderecoPedido = "UPDATE pedidos SET endereco = '$msg_get' WHERE email_painel = '$usuario_get' AND telefone = '$numero_get' AND status = 'cadastrar_endereco'";
    $query = mysqli_query(mysql: $conn, query: $novoEnderecoPedido);

    if ($query) {
        $msg = "🛵 O Endereço está correto??
    
$msg_get

*(1)* SIM
*(2)* NÃO
        ";

        insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

        updateSituacao(telefone: $numero_get, situacao: 'confirmar_endereco', email_painel: $email_painel);

        defUpdatePedidos(set: 'status', msg_get: 'confirmar_endereco', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'cadastrar_endereco');

    }
}

if ($situacao_cliente == "confirmar_endereco" && $nome_cliente) {
    if (ehNumero(texto: $msg_get)) {
        if ($msg_get == "1") {

            $busca_pedidos_final = "SELECT * FROM pedidos WHERE email_painel = '$usuario_get' AND telefone = '$numero_get' AND status = 'confirmar_endereco'";
            $query = mysqli_query(mysql: $conn, query: $busca_pedidos_final);

            while ($dados_pedido = mysqli_fetch_array(result: $query)) {
                $id_pedido_final = $dados_pedido['id'];
                $id_cliente_final = $dados_pedido['id_cliente'];
                $nome_pedido_final = $dados_pedido['nome'];
                $email_painel_pedido_final = $dados_pedido['email_painel'];
                $telefone_pedido_final = $dados_pedido['telefone'];
                $endereco_pedido_final = $dados_pedido['endereco'];
                $pagamento_pedido_final = $dados_pedido['pagamento'];
                $qtd_pepperoni_final = $dados_pedido['qtd_pepperoni'];
                $qtd_frango_final = $dados_pedido['qtd_frango'];
                $qtd_quatroqueijos_final = $dados_pedido['qtd_quatroqueijos'];
                $qtd_brigadeiro_final = $dados_pedido['qtd_brigadeiro'];
                $status_pedido_final = $dados_pedido['status'];
                $data_hora_pedido_final = $dados_pedido['data_hora'];
            }

            $hora_final = date("H:i", strtotime($data_hora_pedido_final));
            $data_final = date("d/m/Y", strtotime($data_hora_pedido_final));


            if ($qtd_pepperoni_final > 0) {
                $pedido1_final = "*$qtd_pepperoni_final* - Pepperoni 🍕";
            }
            if ($qtd_frango_final > 0) {
                $pedido2_final = "*$qtd_frango_final* - Frango 🍕";
            }
            if ($qtd_quatroqueijos_final > 0) {
                $pedido3_final = "*$qtd_quatroqueijos_final* - Quatro Queijos 🍕";
            }
            if ($qtd_brigadeiro_final > 0) {
                $pedido4_final = "*$qtd_brigadeiro_final* - Brigadeiro 🍕";
            }

            $total_pepperoni_final = $qtd_pepperoni_final * $prod_pepperoni;
            $total_frango_final = $qtd_frango_final * $prod_frango;
            $total_quatroqueijos_final = $qtd_quatroqueijos_final * $prod_quatroqueijos;
            $total_brigadeiro_final = $qtd_brigadeiro_final * $prod_brigadeiro;

            $total_geral_final = $total_pepperoni_final + $total_frango_final + $total_quatroqueijos_final + $total_brigadeiro_final;

            $msg = "📋Nota Fiscal - Pedido de Pizzas🍕
📅 Data: $data_final
🕒 Hora: $hora_final
            
🛒 Pedido: 
$pedido1_final
$pedido2_final
$pedido3_final
$pedido4_final

💰 Forma de Pagamento: $pagamento_pedido_final

🚚 Endereço:
$endereco_pedido_final
            
💸 Total: R$ $total_geral_final
    ";

            insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);


            defUpdatePedidos(set: 'status', msg_get: 'aguardando...', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'confirmar_endereco');

            if ($query) {
                $atualizaAguardando = "UPDATE clientes SET situacao = 'aguardando...', endereco = '$endereco_pedido_final' WHERE telefone = '$numero_get' AND email_painel = '$usuario_get' AND situacao = 'confirmar_endereco'";
                $query = mysqli_query(mysql: $conn, query: $atualizaAguardando);

                $msg = "Seu pedido está sendo preparado.
Obrigado por comprar conosco! :)";

                insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
            }
        }
        if ($msg_get == "2") {
            $msg = "🛵 Por favor, digite o endereço da entrega.";

            insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
            if ($query) {
                updateSituacao(telefone: $numero_get, situacao: 'cadastrar_endereco', email_painel: $email_painel);

                defUpdatePedidos(set: 'status', msg_get: 'cadastrar_endereco', email_painel: $email_painel, numero_get: $numero_get, status: $status = 'confirmar_endereco');
            }
        }
    } else {
        $msg = "🛵 O Endereço está correto??
    
$endereco_cliente   

*(1)* SIM
*(2)* NÃO

⚠️ *Envie apenas números, por favor!*
        ";

        insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
    }
}

if ($situacao_cliente == "aguardando..." && $nome_cliente) {
    $msg = "Seu pedido está sendo preparado.
Obrigado por comprar conosco! :)";

    insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
}

?>