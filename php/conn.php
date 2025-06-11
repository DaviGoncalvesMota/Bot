<?php
    // Chama o Banco de Dados
    $servidor = 'localhost';
    $usuario = 'root';
    $senha = '';
    $banco = 'bot_delivery';
    $conn = mysqli_connect(hostname: $servidor, username: $usuario, password: $senha, database: $banco);

    // Verifica se a conexao foi bem sucedida
    if(!$conn) {
         die('Erro ao conectar ao banco de dados: ' . mysqli_connect_error());
    }

?>