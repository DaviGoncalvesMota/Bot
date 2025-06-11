<?php
require_once '../php/conn.php';
$id = $_GET['id'];

$sql = "UPDATE envios SET status = '2' WHERE id = '$id'";
$query = mysqli_query(mysql: $conn, query: $sql);

?>