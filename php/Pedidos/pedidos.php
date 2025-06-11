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
  header(header: 'Location: login.php');
}

$adm = 0;

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <title>BOT</title>
  <meta name="author" content="Adtile">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="../../css/styles.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="pedidosStyles.css?v=<?php echo time(); ?>" type="text/css">
  <link rel="icon" type="icon" href="../../icons/bot.ico">
  <script src="../../js/responsive-nav.js"></script>
</head>

<body>

  <header>
    <a href="../../index.php" class="logo" data-scroll>BOTPAINEL</a>
    <nav class="nav-collapse">
      <ul>
        <li class="menu-item "><a href="../../index.php" data-scroll>VENDAS</a></li>
        <li class="menu-item "><a href="../Produtos/produtos.php" data-scroll>PRODUTOS</a></li>
        <li class="menu-item active"><a href="pedidos.php" data-scroll>PEDIDOS</a></li>
        <li class="menu-item"><a href="../Pagamento/pagamento.php" data-scroll>PAGAMENTO</a></li>
        <?php
        if ($tipo_cliente == 2) {
          echo "<li class='menu-item'><a href='../Admin/admin.php' data-scroll>ADMIN</a></li>";
        }
        ?>
        <li class="menu-item"><a href="../Login/sair.php" data-scroll>SAIR</a></li>
      </ul>
    </nav>
  </header>

  <section style="border-bottom: none" id="home">

    <?php
    // Pega os pedidos de acordo com o email e status
    $busca_pedidos = "SELECT * FROM pedidos WHERE status = 'aguardando...' AND email_painel = '$email_cliente'";
    $resultado_pedidos = mysqli_query(mysql: $conn, query: $busca_pedidos);
    $total_pedidos = mysqli_num_rows(result: $resultado_pedidos);

    // Pega os dados do usuario de acordo com o email
    while ($dados_pedidos = mysqli_fetch_array(result: $resultado_pedidos)) {
      $id_pedido = $dados_pedidos['id'];
      $nome_cliente = $dados_pedidos['nome'];
      $id_cliente = $dados_pedidos['id_cliente'];
      $telefone_cliente = $dados_pedidos['telefone'];
      $endereco_cliente = $dados_pedidos['endereco'];
      $qtd_pepperoni = $dados_pedidos['qtd_pepperoni'];
      $qtd_frango = $dados_pedidos['qtd_frango'];
      $qtd_quatroqueijos = $dados_pedidos['qtd_quatroqueijos'];
      $qtd_brigadeiro = $dados_pedidos['qtd_brigadeiro'];
      $forma_pagamento = $dados_pedidos['pagamento'];
      $status = $dados_pedidos['status'];
      $data_hora = $dados_pedidos['data_hora'];
      $email_painel = $dados_pedidos['email_painel'];

      ?>
      <div style="text-align: center">
        <form id="form1" name="form1" method="post">
          <table style="width: 100%; border: 1px solid black;">
            <tr>
              <td colspan="2">
                <div style="text-align: center">
                  <H1>NOVO PEDIDO</H1>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div style="text-align: center"><b>PRODUTO</b></div>
              </td>
              <td>
                <div style="text-align: center"><b>QUANTIDADE</b></div>
              </td>
            </tr>
            <tr>
              <td><br>
                <div style="text-align: center">PEPPERONI</div>
              </td>
              <td>
                <div style="text-align: center"><?php echo $qtd_pepperoni ?></div>
              </td>
            </tr>
            <tr>
              <td><br>
                <div style="text-align: center">FRANGO</div>
              </td>
              <td>
                <div style="text-align: center"><?php echo $qtd_frango ?></div>
              </td>
            </tr>
            <tr>
              <td><br>
                <div style="text-align: center">QUATROQUEIJOS</div>
              </td>
              <td>
                <div style="text-align: center"><?php echo $qtd_quatroqueijos ?></div>
              </td>
            </tr>
            <tr>
              <td><br>
                <div style="text-align: center">BRIGADEIRO</div>
              </td>
              <td>
                <div style="text-align: center"><?php echo $qtd_brigadeiro ?></div>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <div style="text-align: center"><b>CLIENTE:</b></div>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <div style="text-align: center"><?php echo "<b>{$nome_cliente}</b>" . " - " . "{$telefone_cliente}" ?>
                </div>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <div style="text-align: center"><b>ENDEREÇO<b></div>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <div style="text-align: center"><?php echo $endereco_cliente ?></div>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <div style="text-align: center"><b>FORMA DE PAGAMENTO<b></div>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <div style="text-align: center"><?php echo $forma_pagamento ?></div>
              </td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>
                <label>
                  <div style="text-align: center">
                    <input type="hidden" name="id_pedido" id="id_pedido" value="<?php echo $id_pedido ?>" />
                    <input class="aceitar-btn" type="submit" name="button" id="button" value="ACEITAR"
                      formaction="aceitar.php" />
                  </div>
                </label>
              </td>
              <td>
                <label>
                  <div style="text-align: center">
                    <input class="recusar-btn" type="submit" name="button" id="button" value="RECUSAR"
                      formaction="recusar.php" />
                  </div>
                </label>
              </td>
            </tr>
          </table>
        </form>
      </div>
      <br><br>
      <?php
    }
    ?>
  </section>



  <script src="../../js/fastclick.js"></script>
  <script src="../../js/scroll.js"></script>
  <script src="../../js/fixed-responsive-nav.js"></script>
</body>

</html>

<meta http-equiv="refresh" content="10" />