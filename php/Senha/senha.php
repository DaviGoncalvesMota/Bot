<?php
// Inicia sessao no navegador
session_start();
// Chama o Banco de Dados
require_once '../conn.php';


?>

<?php
    // Atualiza senha 
    $senha_update = $_POST["senha"];
    $email_cliente = $_POST["email"];
    $sql = "UPDATE login SET senha = '$senha_update' WHERE email = '$email_cliente'";
    $query_update = mysqli_query(mysql: $conn, query: $sql);

    // Verifica se a atualizacao foi bem sucedida e redireciona
    if($query_update){
        header(header: 'Location: ../Login/login.php');
    }
    else {
       header(header: 'Location: ../erro.php');
    }
?>