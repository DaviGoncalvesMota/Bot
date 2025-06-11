<?php
session_start();
require_once '../conn.php';

if ($_SESSION["email"]) {
    $email_cliente = $_SESSION["email"];
    $busca_email = "SELECT * FROM login WHERE email = '$email_cliente'";
    $resultado_busca = mysqli_query(mysql: $conn, query: $busca_email);
    $total_clientes = mysqli_num_rows(result: $resultado_busca);

    while ($dados_usuario = mysqli_fetch_array(result: $resultado_busca)) {
        $email_cliente = $dados_usuario['email'];
        $nome_cliente = $dados_usuario['nome'];
        $senha_cliente = $dados_usuario['senha'];
        $tipo_cliente = $dados_usuario['tipo'];
    }
} else {
    header(header: 'Location: login.php');
}

$adm = 0;

if ($tipo_cliente == '1') {
    header(header: 'Location: index.php');
}
?>

<?php
    $id_usuario = $_POST['id'];
    $status = $_POST['status']; // criar um novo campo no formulário para o novo status

    $sql = "UPDATE login SET status = '$status' WHERE id = '$id_usuario'";

    $query = mysqli_query(mysql: $conn, query: $sql);

    // Verifica se a query foi bem sucedida e guarda o valor no navegador
    if ($query) {
        $_SESSION['valor'];
        header(header: 'Location: admin.php?valor=ok');
    } else {
        header(header: 'Location: ../erro.php');
    }
?>