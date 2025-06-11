<?php
require_once '../php/conn.php';
$usuario_get = $_GET['usuario'];

error_reporting(error_level: 0);
ini_set(option: "display_errors", value: 0);


$busca_cliente = "SELECT * FROM envios WHERE usuario = '$usuario_get' AND status = '1' ORDER BY id DESC";
$cliente = mysqli_query(mysql: $conn, query: $busca_cliente);

while($dados_cliente = mysqli_fetch_array(result: $cliente)){
    $id = $dados_cliente['id'];
    $telefone = $dados_cliente['telefone'];
    $msg = $dados_cliente['mensagem'];
}

$n = '.n.';

if($telefone) {
    echo "enviando $n $id $n $telefone $n $msg";
}

date_default_timezone_set(timezoneId: 'America/Sao_Paulo'); // Define o fuso horário
$now = time();

$data_hora = date(format: "Y-m-d H:i:s", timestamp: $now);

$data_hora_limite = date('Y-m-d H:i:s', timestamp: strtotime(datetime: '-15 minutes', baseTimestamp: strtotime(datetime: $data_hora)));

$busca_pedido = "SELECT * FROM pedidos WHERE data_hora < '$data_hora_limite' AND status != 'aceito'";
$pedidos = mysqli_query(mysql: $conn, query: $busca_pedido);

while($dados_usuario = mysqli_fetch_array(result: $pedidos)){
    $id_pedido = $dados_usuario['id'];
    $nome = $dados_usuario['nome'];
    $telefone = $dados_usuario['telefone'];
}

if($id_pedido) {
    $msg = "Olá, $nome, seu atendimento foi encerrado, pois o mesmo ficou sem resposta";

    $encerra = "INSERT INTO envios (telefone, mensagem, status, usuario) VALUES ('$telefone', '$msg', '1', '$usuario_get')";
    $query = mysqli_query(mysql: $conn, query: $encerra);

    if($query) {
        $zerar = "UPDATE pedidos SET status = '' WHERE id = '$id_pedido'";
        $query = mysqli_query(mysql: $conn, query: $zerar);

        if($query) {
            $apagar = "DELETE FROM pedidos WHERE id = '$id_pedido'";
            $query = mysqli_query(mysql: $conn, query: $apagar);
        }
    }
}
?>