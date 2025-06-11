<?php

require_once '../php/conn.php';

error_reporting(error_level: 0);
ini_set(option: "display_errors", value: 0);

$login_get = $_GET['login'];
$senha_get = $_GET['senha'];

$busca_login = "SELECT * FROM login WHERE email = '$login_get' AND senha = '$senha_get' AND status = 'ativo'";
$login = mysqli_query(mysql: $conn, query: $busca_login);
$total_clientes = mysqli_num_rows(result: $login);  
echo $total_clientes;


?>  