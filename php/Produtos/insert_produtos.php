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

$nome_produto = $_POST['nome_produto'];
$valor_produto = $_POST['valor_produto'];
$descricao_produto = $_POST['desc_produto'];
$num_produto = $_POST['numero_produto'];

// Atualiza os produtos
$sql = "INSERT INTO produtos (nome, descricao, preco, numero_produto, email_painel) VALUES ('$nome_produto', '$descricao_produto', '$valor_produto', '$num_produto', '$email_cliente')";

$query = mysqli_query(mysql: $conn, query: $sql);

// Verifica se a query foi bem sucedida e redireciona
if ($query) {
    header(header: 'Location: produtos.php');
}
else {
    echo "Erro ao cadastrar";
}

?>