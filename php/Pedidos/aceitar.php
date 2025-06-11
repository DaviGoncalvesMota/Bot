<?php
// Inicia sessao no navegador
session_start();
// Chama o Banco de Dados
require_once '../conn.php';

// Verifica se o usuario esta logado
if ($_SESSION["email"]) {
    // Pega o email do usuario
    $email_cliente = $_SESSION["email"];
    $busca_email = "SELECT * FROM login WHERE email = '$email_cliente'";
    $resultado_busca = mysqli_query(mysql: $conn, query: $busca_email);
    $total_clientes = mysqli_num_rows(result: $resultado_busca);

    // Pega os dados do usuario de acordo com o email
    while ($dados_usuario = mysqli_fetch_array(result: $resultado_busca)) {
        $email_cliente = $dados_usuario['email'];
        $nome_cliente = $dados_usuario['nome'];
        $senha_cliente = $dados_usuario['senha'];
        $tipo_cliente = $dados_usuario['tipo'];
    }
} else {
    header(header: 'Location: ../Login/login.php');
}

$adm = 0;

?>

<?php

$id_pedido = $_POST['id_pedido'];

$buscar_pedido = "SELECT * FROM pedidos WHERE id = '$id_pedido'";
$resultado_busca = mysqli_query(mysql: $conn, query: $buscar_pedido);

while ($dados_pedido = mysqli_fetch_array(result: $resultado_busca)) {
    $nome = $dados_pedido['nome'];
    $telefone = $dados_pedido['telefone'];
}

$pedidoAceito = "UPDATE pedidos SET status = 'aceito' WHERE id = '$id_pedido'";
$query = mysqli_query(mysql: $conn, query: $pedidoAceito);

// Verifica se a query foi bem sucedida e redireciona
if ($query) {
    $msg = "Olá, $nome, seu pedido foi aceito com sucesso!
já esta sendo preparado para entrega.";
    
    $aceito = "INSERT INTO envios (telefone, mensagem, status, usuario) VALUES ('$telefone', '$msg', '1', '$email_cliente')";
    $query = mysqli_query(mysql: $conn, query: $aceito);

    if($query) {
        $zerar = "UPDATE clientes SET situacao = '' WHERE telefone = '$telefone'";
        $query = mysqli_query(mysql: $conn, query: $zerar);

        if($query) {
            header(header: 'Location: ../../index.php');
        }
    }
}

?>