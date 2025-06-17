<?php

require_once '../php/conn.php';

$numero_get = $_GET['telefone'];
$usuario_get = $_GET['usuario'];
$msg_get = $_GET['msg'];

// Funções

// Função para obter o preço do produto
function getPrecoProduto($email_painel, $nome_produto) {
    global $conn;
    $sql = "SELECT preco FROM produtos WHERE email_painel = '$email_painel' AND nome = '$nome_produto' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        return floatval($row['preco']);
    }
    return 0;
}

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

function defUpdatePedidos($set, $msg_get, $email_painel, $numero_get, $status): void
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

// 1.1 Novo cliente
if ($total_clientes == 0) {
    $sql = "INSERT INTO clientes (telefone, email_painel) VALUES ('$numero_get', '$usuario_get')";
    mysqli_query($conn, $sql);

    $msg = "Para começar, me diga seu nome 😊";
    insertEnvios($numero_get, $msg, '1', $usuario_get);
    return;
}

// 1.2 Preencher nome do cliente
if ($total_clientes == 1 && empty($nome_cliente)) {
    $msg_usuario = letraMaiuscula(texto: $msg_get);
    $update = "UPDATE clientes 
        SET nome = '$msg_usuario', situacao = 'inicio_compra', produto_temp = NULL 
        WHERE email_painel = '$usuario_get' AND telefone = '$numero_get'";
    mysqli_query($conn, $update);

    $msg = "⭐ Bem-vindo, *$msg_usuario*, ao nosso delivery! ⭐\n\n";
    $msg .= "Aqui estão os produtos disponíveis:\n\n";
    $msg .= selectProdutos($usuario_get);
    $msg .= "Envie o número do produto para escolhê-lo.";

    insertEnvios($numero_get, $msg, '1', $usuario_get);
    return;
}

if ($situacao_cliente == "inicio_compra" && ehNumero($msg_get)) {

    insertPedidos(
        id_cliente: $id_cliente,
        nome: $nome_cliente,
        email_painel: $usuario_get,
        telefone: $numero_get,
        endereco: $endereco_cliente,
        status: 'compra',
        data_hora: $data_hora
    );

    $sql = "SELECT * FROM produtos WHERE email_painel = '$usuario_get' AND numero_produto = '$msg_get'";
    $query = mysqli_query($conn, $sql);

    if (mysqli_num_rows($query) > 0) {
        $produto = mysqli_fetch_assoc($query);
        $nome_produto = $produto['nome'];

        // Armazena produto temporariamente
        $update = "UPDATE clientes SET produto_temp = '$nome_produto', situacao = 'aguardando_quantidade' WHERE email_painel = '$usuario_get' AND telefone = '$numero_get'";
        mysqli_query($conn, $update);

        $msg = "Você escolheu *$nome_produto*.\nQuantas unidades deseja?\nExemplo: *1*, *2*, *3*...";
        insertEnvios($numero_get, $msg, '1', $usuario_get);
    }

    return;
}


if ($situacao_cliente == "aguardando_quantidade" && ehNumero($msg_get)) {
    // Recupera produto temporário
    $sql = "SELECT produto_temp FROM clientes WHERE email_painel = '$usuario_get' AND telefone = '$numero_get'";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);
    $nome_produto = $row['produto_temp'];

    // Cria ou atualiza pedido
    defUpdatePedidos(set: 'qtd_produtos', msg_get: $msg_get, email_painel: $usuario_get, numero_get: $numero_get, status: $nome_produto);
    defUpdatePedidos(set: 'status', msg_get: 'compra', email_painel: $usuario_get, numero_get: $numero_get, status: $nome_produto);

    // Nota fiscal
    $qtd = $msg_get;
    $preco = getPrecoProduto($usuario_get, $nome_produto);
    $total = $preco * $qtd;
    $data = date("d/m/Y");
    $hora = date("H:i");

    $msg = "📋Nota Fiscal\n📅 Data: $data\n🕒 Hora: $hora\n\n🛒 Pedido:\n*$qtd* - $nome_produto\n\n💸 Total: R$ $total";
    insertEnvios($numero_get, $msg, '1', $usuario_get);

    // Volta à seleção de produtos
    $update = "UPDATE clientes SET situacao = 'inicio_compra', produto_temp = NULL WHERE email_painel = '$usuario_get' AND telefone = '$numero_get'";
    mysqli_query($conn, $update);

    $msg = "*Escolha outro produto ou finalize a compra*:\n\n";
    $msg .= selectProdutos($usuario_get);
    $msg .= "\n🛒 (5) *Finalizar Compra*";

    insertEnvios($numero_get, $msg, '1', $usuario_get);
    return;
}

if ($situacao_cliente == "inicio_compra" && $msg_get == "5") {
    $sql = "UPDATE pedidos SET status = 'pagamento' WHERE email_painel = '$usuario_get' AND telefone = '$numero_get' AND status = 'compra'";
    mysqli_query($conn, $sql);

    updateSituacao($numero_get, 'pagamento', $usuario_get);

    $msg = "Escolha a forma de pagamento:\n\n";
    if ($dinheiro_painel > 0) $msg .= "*(1)* Dinheiro 💸\n";
    if ($pix_painel > 0)      $msg .= "*(2)* Pix 📲\n";
    if ($cartao_painel > 0)   $msg .= "*(3)* Cartão 💳\n";
    if ($caderneta_painel > 0) $msg .= "*(4)* Caderneta 📖\n";

    insertEnvios($numero_get, $msg, '1', $usuario_get);
    return;
}

if ($situacao_cliente == "pagamento" && $nome_cliente) {
    if (ehNumero(texto: $msg_get)) {

        if ($msg_get == "1") {
            defUpdatePedidos(set: 'pagamento', msg_get: 'dinheiro', email_painel: $email_painel, numero_get: $numero_get, status:
                $status = 'pagamento');

            if ($endereco_cliente == "") {
                $msg = "Poderia me passar seu endereço completo 📍
para que possamos enviar seu pedido? 🛵 🍕";

                insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

                updateSituacao(telefone: $numero_get, situacao: 'cadastrar_endereco', email_painel: $email_painel);

                defUpdatePedidos(
                    set: 'status',
                    msg_get: 'cadastrar_endereco',
                    email_painel: $email_painel,
                    numero_get: $numero_get,
                    status: $status = 'pagamento'
                );
            }
            if ($endereco_cliente) {
                $msg = "O endereço da entrega é esse?

🛵 Entrega: *$endereco_cliente*

*(1)* SIM
*(2)* NÃO
";

                insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

                updateSituacao(telefone: $numero_get, situacao: 'confirmar_endereco', email_painel: $email_painel);

                defUpdatePedidos(
                    set: 'status',
                    msg_get: 'confirmar_endereco',
                    email_painel: $email_painel,
                    numero_get: $numero_get,
                    status: $status = 'pagamento'
                );

            }
        }

        if ($msg_get == "2") {
            defUpdatePedidos(set: 'pagamento', msg_get: 'pix', email_painel: $email_painel, numero_get: $numero_get, status: $status
                = 'pagamento');


            if ($endereco_cliente == "") {
                $msg = "Poderia me passar seu endereço completo 📍
para que possamos enviar seu pedido? 🛵 🍕";

                insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

                updateSituacao(telefone: $numero_get, situacao: 'cadastrar_endereco', email_painel: $email_painel);

                defUpdatePedidos(
                    set: 'status',
                    msg_get: 'cadastrar_endereco',
                    email_painel: $email_painel,
                    numero_get: $numero_get,
                    status: $status = 'pagamento'
                );
            }
            if ($endereco_cliente) {
                $msg = "O endereço da entrega é esse?

🛵 Entrega: *$endereco_cliente*

*(1)* SIM
*(2)* NÃO
";

                insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

                updateSituacao(telefone: $numero_get, situacao: 'confirmar_endereco', email_painel: $email_painel);

                defUpdatePedidos(
                    set: 'status',
                    msg_get: 'confirmar_endereco',
                    email_painel: $email_painel,
                    numero_get: $numero_get,
                    status: $status = 'pagamento'
                );
            }
        }

        if ($msg_get == "3") {
            defUpdatePedidos(set: 'pagamento', msg_get: 'cartao', email_painel: $email_painel, numero_get: $numero_get, status:
                $status = 'pagamento');

            if ($query) {
                if ($endereco_cliente == "") {
                    $msg = "Poderia me passar seu endereço completo 📍
para que possamos enviar seu pedido? 🛵 🍕";

                    insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

                    updateSituacao(telefone: $numero_get, situacao: 'cadastrar_endereco', email_painel: $email_painel);

                    defUpdatePedidos(
                        set: 'status',
                        msg_get: 'cadastrar_endereco',
                        email_painel: $email_painel,
                        numero_get: $numero_get,
                        status: $status = 'pagamento'
                    );
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

                        defUpdatePedidos(
                            set: 'status',
                            msg_get: 'confirmar_endereco',
                            email_painel: $email_painel,
                            numero_get: $numero_get,
                            status: $status = 'pagamento'
                        );
                    }
                }
            }
        }

        if ($msg_get == "4") {
            defUpdatePedidos(set: 'pagamento', msg_get: 'caderneta', email_painel: $email_painel, numero_get: $numero_get, status:
                $status = 'pagamento');


            if ($endereco_cliente == "") {
                $msg = "Poderia me passar seu endereço completo 📍
para que possamos enviar seu pedido? 🛵 🍕";

                insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

                updateSituacao(telefone: $numero_get, situacao: 'cadastrar_endereco', email_painel: $email_painel);

                defUpdatePedidos(
                    set: 'status',
                    msg_get: 'cadastrar_endereco',
                    email_painel: $email_painel,
                    numero_get: $numero_get,
                    status: $status = 'pagamento'
                );
            }
            if ($endereco_cliente) {
                $msg = "O endereço da entrega é esse?

🛵 Entrega: *$endereco_cliente*

*(1)* SIM
*(2)* NÃO
";

                insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

                updateSituacao(telefone: $numero_get, situacao: 'confirmar_endereco', email_painel: $email_painel);

                defUpdatePedidos(
                    set: 'status',
                    msg_get: 'confirmar_endereco',
                    email_painel: $email_painel,
                    numero_get: $numero_get,
                    status: $status = 'pagamento'
                );
            }
        }
    }
}

if ($situacao_cliente == "cadastrar_endereco" && $nome_cliente) {
    $novoEnderecoCliente = "UPDATE clientes SET endereco = '$msg_get' WHERE email_painel = '$usuario_get' AND telefone =
'$numero_get'";
    $query = mysqli_query(mysql: $conn, query: $novoEnderecoCliente);

    $novoEnderecoPedido = "UPDATE pedidos SET endereco = '$msg_get' WHERE email_painel = '$usuario_get' AND telefone =
'$numero_get' AND status = 'cadastrar_endereco'";
    $query = mysqli_query(mysql: $conn, query: $novoEnderecoPedido);

    if ($query) {
        $msg = "🛵 O Endereço está correto??

$msg_get

*(1)* SIM
*(2)* NÃO
";

        insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

        updateSituacao(telefone: $numero_get, situacao: 'confirmar_endereco', email_painel: $email_painel);

        defUpdatePedidos(
            set: 'status',
            msg_get: 'confirmar_endereco',
            email_painel: $email_painel,
            numero_get: $numero_get,
            status: $status = 'cadastrar_endereco'
        );

    }
}

if ($situacao_cliente == "confirmar_endereco" && $nome_cliente) {
    if (ehNumero(texto: $msg_get)) {
        if ($msg_get == "1") {

            $busca_pedidos_final = "SELECT * FROM pedidos WHERE email_painel = '$usuario_get' AND telefone = '$numero_get' AND
status = 'confirmar_endereco'";
            $query = mysqli_query(mysql: $conn, query: $busca_pedidos_final);

            while ($dados_pedido = mysqli_fetch_array(result: $query)) {
                $id_pedido_final = $dados_pedido['id'];
                $id_cliente_final = $dados_pedido['id_cliente'];
                $nome_pedido_final = $dados_pedido['nome'];
                $email_painel_pedido_final = $dados_pedido['email_painel'];
                $telefone_pedido_final = $dados_pedido['telefone'];
                $endereco_pedido_final = $dados_pedido['endereco'];
                $pagamento_pedido_final = $dados_pedido['pagamento'];
                $qtd_produtos_final = $dados_pedido['qtd_produtos'];
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

            $total_geral_final = $total_pepperoni_final + $total_frango_final + $total_quatroqueijos_final +
                $total_brigadeiro_final;

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


            defUpdatePedidos(set: 'status', msg_get: 'aguardando...', email_painel: $email_painel, numero_get: $numero_get, status:
                $status = 'confirmar_endereco');

            if ($query) {
                $atualizaAguardando = "UPDATE clientes SET situacao = 'aguardando...', endereco = '$endereco_pedido_final' WHERE
telefone = '$numero_get' AND email_painel = '$usuario_get' AND situacao = 'confirmar_endereco'";
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

                defUpdatePedidos(
                    set: 'status',
                    msg_get: 'cadastrar_endereco',
                    email_painel: $email_painel,
                    numero_get: $numero_get,
                    status: $status = 'confirmar_endereco'
                );
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