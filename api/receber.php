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

if (ehNumero($msg_get)) {
    // 2. Está selecionando um produto
    $produto_selecionado_sql = "SELECT * FROM produtos WHERE email_painel = '$usuario_get' AND numero_produto = '$msg_get'";
    $query_produto = mysqli_query($conn, $produto_selecionado_sql);

    if (mysqli_num_rows($query_produto) > 0 && $msg_get != "5") {
        $produto = mysqli_fetch_assoc($query_produto);
        $nome_produto = $produto['nome'];

        // Verifica se já existe pedido desse produto para este cliente e status 'montando'
        $busca_pedido = "SELECT * FROM pedidos WHERE email_painel = '$usuario_get' AND telefone = '$numero_get' AND status = '$nome_produto' AND status = '$nome_produto' OR status = 'montando'";
        $query_pedido = mysqli_query($conn, $busca_pedido);

        if (mysqli_num_rows($query_pedido) > 0) {
            // Incrementa a quantidade
            $pedido = mysqli_fetch_assoc($query_pedido);
            $qtd_atual = (int) $pedido['qtd_produtos'];
            $nova_qtd = $qtd_atual + $msg_get;
            $update_qtd = "UPDATE pedidos SET qtd_produtos = '$nova_qtd' WHERE id = '{$pedido['id']}'";
            mysqli_query($conn, $update_qtd);
        } else if (mysqli_num_rows($query_pedido) == 0) {
            // Cria novo pedido para o produto
            $insert_pedido = "INSERT INTO pedidos (id_cliente, nome, email_painel, telefone, endereco, status, qtd_produtos, data_hora) VALUES ('$id_cliente', '$nome_cliente', '$usuario_get', '$numero_get', '$endereco_cliente', '$nome_produto', '$msg_get', '$data_hora')";
            mysqli_query($conn, $insert_pedido);
        }

        // Pergunta novamente qual produto deseja adicionar
        $msg .= "Deseja adicionar mais algum produto?\n";
        $msg .= selectProdutos($usuario_get);
        $msg .= "🛒(5) *Finalizar Compra*\n";
        $msg .= "Para selecionar seu produto, basta enviar o número correspondente.\nQuando terminar, envie *5* para finalizar.";

        insertEnvios($numero_get, $msg, '1', $usuario_get);

        // Atualiza situação para compra_produto
        updateSituacao($numero_get, 'compra_produto', $usuario_get);
        return;
    }
}

if ($situacao_cliente == "compra_produto" && $nome_cliente) {
    if (ehNumero(texto: $msg_get)) {
        defUpdatePedidos(set: 'qtd_produtos', msg_get: $msg_get, email_painel: $usuario_get, numero_get: $numero_get, status: $nome_produto);


        $busca_pedidos_sql = "SELECT * FROM pedidos WHERE email_painel = '$usuario_get' AND telefone = '$numero_get' AND status = '$nome_produto'";
        $query_pedidos = mysqli_query(mysql: $conn, query: $busca_pedidos_sql);

        if (mysqli_num_rows(result: $query_pedidos) > 0) {
            $dados_pedidos = mysqli_fetch_assoc(result: $query_pedidos);

            $qtd_produtos = $dados_pedidos['qtd_produtos'];
            $data_hora_pedido = $dados_pedidos['data_hora'];

            $pedido = "*$qtd_produtos* - $nome_produto";

            $hora = date("H:i", strtotime($data_hora_pedido));
            $data = date("d/m/Y", strtotime($data_hora_pedido));
            $total = $qtd_produtos * $preco_produto;

            $msg = "📋Nota Fiscal\n📅 Data: $data\n🕒 Hora: $hora\n\n🛒 Pedido:\n$pedido\n\n💸 Total: R$ $total";

            insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

            updateSituacao(telefone: $numero_get, situacao: 'compra', email_painel: $usuario_get);
            defUpdatePedidos(set: 'status', msg_get: 'compra', email_painel: $usuario_get, numero_get: $numero_get, status: $nome_produto);

            // Mostra novamente os produtos
            $msg = "*Escolha outro produto ou encerre a compra*:\n\n";
            $msg .= selectProdutos($usuario_get);
            $msg .= "🛒(5) *Finalizar Compra*\n";
            $msg .= "Para selecionar seu produto, basta\n";
            $msg .= "enviar o número correspondente a\n";
            $msg .= "ele. Estamos prontos para atendê-lo!";

            insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);

        }
    } else {
        $msg = "⚠️ *Ops! Envie apenas números, por favor!*\n\nQuantas(os) $nome_produto você deseja?\n\nDigite um número como: *1*, *2* ou *3*.";
        insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
    }

    return; // Interrompe aqui para não continuar interpretando como seleção de produto
}

// 3. Finalizar compra
if ($msg_get == "5") {
    defUpdatePedidos(set: 'status', msg_get: 'pagamento', email_painel: $email_painel, numero_get: $numero_get, status: $nome_produto);
    updateSituacao(telefone: $numero_get, situacao: 'pagamento', email_painel: $email_painel);

    $formas_pagamento = "";
    if ($dinheiro_painel > 0)
        $formas_pagamento .= "*(1)* Dinheiro 💸\n";
    if ($pix_painel > 0)
        $formas_pagamento .= "*(2)* Pix 📲\n";
    if ($cartao_painel > 0)
        $formas_pagamento .= "*(3)* Cartão de Crédito 💳\n";
    if ($caderneta_painel > 0)
        $formas_pagamento .= "*(4)* Caderneta 📖\n";

    $msg = "Escolha a forma de pagamento:\n\n" . $formas_pagamento;
    insertEnvios(telefone: $numero_get, mensagem: $msg, status: '1', usuario: $usuario_get);
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