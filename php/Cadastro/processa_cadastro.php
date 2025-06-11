<?php
// Chama o banco de dados
require_once '../conn.php';

?>


<?php
// Pega os dados do cliente
$nome_cliente = $_POST["nome"];
$email_cliente = $_POST["email"];
$senha_cliente = $_POST["senha"];

// Verifica emails
$busca_email = "SELECT * FROM login WHERE email = '$email_cliente'";
$resultado_busca = mysqli_query(mysql: $conn, query: $busca_email);
$total_clientes = mysqli_num_rows(result: $resultado_busca);

echo $total_clientes;

// Verificar se usuario ja existe
if ($total_clientes > 0) {
    // Redirect para pagina de erro
    header(header: 'Location: ../erro.php');
} else {
    // Insere usuario
    $sql = "INSERT INTO login (nome, email, senha, tipo) VALUES ('$nome_cliente', '$email_cliente', '$senha_cliente', '1')";
    $query = mysqli_query(mysql: $conn, query: $sql);

    if ($query) {
        // Salva usuario na sessao
        $_SESSION['email'] = $email_cliente;
        $_SESSION['senha'] = $senha_cliente;

        header(header: 'Location: ../Vendas/index.php');
    } else {
        header(header: 'Location: ../erro.php');
    }
}
