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

$id_produto = $_POST['id'];
$nome_produto = $_POST['nome'];
$preco_produto = $_POST['preco'];
$descricao_produto = $_POST['descricao'];
$num_produto = $_POST['numero_produto'];
$editar = isset($_POST['editar']);
$excluir = isset($_POST['excluir']);

// Atualiza os produtos

if ($editar) {
    $sql = "UPDATE produtos SET 
        nome = '$nome_produto',
        preco = '$preco_produto',
        descricao = '$descricao_produto',
        numero_produto = '$num_produto'
    WHERE 
        email_painel = '$email_cliente' AND id = '$id_produto'";    
}
if ($excluir) {
    $sql = "DELETE FROM produtos WHERE email_painel = '$email_cliente' AND id = '$id_produto'";
}

$query = mysqli_query(mysql: $conn, query: $sql);

// Verifica se a query foi bem sucedida e redireciona
if ($query) {
    header(header: 'Location: produtos.php');
}

?>