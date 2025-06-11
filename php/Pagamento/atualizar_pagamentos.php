<?php
// Inicia sessao no navegador
session_start();
// Chama o Banco de Dados
require_once 'conn.php';

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

        // Pega os tipos de pagamento
        $dinheiro_cliente = $dados_usuario['dinheiro'];
        $pix_cliente = $dados_usuario['pix'];
        $cartao_cliente = $dados_usuario['cartao'];
        $caderneta_cliente = $dados_usuario['caderneta'];
    }
} else {
    header(header: 'Location: login.php');
}

$dinheiro = isset($_POST["dinheiro"]);
$pix = isset($_POST["pix"]);
$cartao = isset($_POST["cartao"]);
$caderneta = isset($_POST["caderneta"]);

// Atualiza os tipos de pagamento
$sql = "UPDATE login SET 
    dinheiro = '$dinheiro', 
    pix = '$pix', 
    cartao = '$cartao', 
    caderneta = '$caderneta' 
WHERE email = '$email_cliente'";


$query = mysqli_query(mysql: $conn, query: $sql);

// Verifica se a query foi bem sucedida e guarda o valor no navegador
if ($query)
    $_SESSION['valor'];
    header(header: 'Location: pagamento.php?valor=ok');
?>