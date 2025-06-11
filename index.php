<?php
// Inicia sessao no navegador
session_start();
// Chama o Banco de Dados
require_once './php/conn.php';

// Verifica se o usuario esta logado
if ($_SESSION["email"]) {
  // Pega o email do usuario logado
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
  header(header: 'Location: ./php/Login/login.php');
}

$adm = 0;

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta name="author" content="Adtile">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="./css/styles.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="./php/Vendas/vendasStyles.css?v=<?php echo time(); ?>">
  <link rel="icon" type="icon" href="./icons/bot.ico">
  <title>BOT</title>
  <script src="js/responsive-nav.js"></script>
</head>

<body>
  <header>
    <a href="index.php" class="logo" data-scroll>BOTPAINEL</a>
    <nav class="nav-collapse">
      <ul>
        <li class="menu-item active"><a href="index.php" data-scroll>VENDAS</a></li>
        <li class="menu-item"><a href="./php/Produtos/produtos.php" data-scroll>PRODUTOS</a></li>
        <li class="menu-item"><a href="./php/Pedidos/pedidos.php" data-scroll>PEDIDOS</a></li>
        <li class="menu-item"><a href="./php/Pagamento/pagamento.php" data-scroll>PAGAMENTO</a></li>
        <?php
        if ($tipo_cliente == 2) {
          echo "<li class='menu-item'><a href='./php/Admin/admin.php' data-scroll>ADMIN</a></li>";
        }
        ?>
        <li class="menu-item"><a href="./php/Login/sair.php" data-scroll>SAIR</a></li>
      </ul>
    </nav>
  </header>

  <?php
  // busca pedidos pelo status e email
  $busca_pedidos = "SELECT * FROM pedidos WHERE status = 'aceito' AND email_painel = '$email_cliente'";
  $resultado_pedidos = mysqli_query(mysql: $conn, query: $busca_pedidos);
  $total_pedidos = mysqli_num_rows(result: $resultado_pedidos);
  // Pega os dados dos pedidos
  
  while ($dados_pedidos = mysqli_fetch_array(result: $resultado_pedidos)) {
      $id_pedido = $dados_pedidos['id'];
      $nome_pedidos = $dados_pedidos['nome'];
      $telefone_pedidos = $dados_pedidos['telefone'];
      $endereco_pedidos = $dados_pedidos['endereco'];
      $forma_pagamento = $dados_pedidos['pagamento'];
      $status = $dados_pedidos['status'];
      $data_hora = $dados_pedidos['data_hora'];
      $email_painel = $dados_pedidos['email_painel'];
    ?>

    <section style="border-bottom: none; height: 550px;" id="home">
      <form>
        <h1>Detalhes da venda</h1>
        <table>
          <tr>
            <td>Cliente:</td>
            <td><?php echo $nome_pedidos ?></td>
          </tr>
          <tr>
            <td>Telefone:</td>
            <td><?php echo $telefone_pedidos ?></td>
          </tr>
          <tr>
            <td>Forma de Pagamento:</td>
            <td><?php echo $forma_pagamento ?></td>
          </tr>
          <tr>
            <td>Data:</td>
            <td><?php echo $data_brasil = date(format: 'd/m/Y', timestamp: strtotime(datetime: $data_hora))?></td>
          </tr>
          <tr>
            <td>Hora:</td>
            <td><?php echo $data_brasil = date(format: 'H:i:s', timestamp: strtotime(datetime: $data_hora))?></td>
          </tr>
          <tr>
            <td>Valor:</td>
            <td>
            
            </td>
          </tr>
        </table>
      </form>
    </section>

  <?php
  }
  ?>

  <script src="../../js/fastclick.js"></script>
  <script src="../../js/scroll.js"></script>
  <script src="../../js/fixed-responsive-nav.js"></script>
</body>

</html>