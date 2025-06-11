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

    // Pega os tipos de pagamento
    $dinheiro_cliente = $dados_usuario['dinheiro'];
    $pix_cliente = $dados_usuario['pix'];
    $cartao_cliente = $dados_usuario['cartao'];
    $caderneta_cliente = $dados_usuario['caderneta'];
  }
} else {
  header(header: 'Location: login.php');
}

?>

<!DOCTYPE html>
<html lang="pt">
<title>BOT</title>

<head>
  <meta charset="utf-8">
  <meta name="author" content="Adtile">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="../../css/styles.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="pagamentoStyles.css?v=<?php echo time(); ?>">
  <link rel="icon" type="icon" href="../../icons/bot.ico">
  <script src="../../js/responsive-nav.js"></script>
</head>

<body>

  <<header>
    <a href="../../index.php" class="logo" data-scroll>BOTPAINEL</a>
    <nav class="nav-collapse">
      <ul>
        <li class="menu-item "><a href="../../index.php" data-scroll>VENDAS</a></li>
        <li class="menu-item "><a href="../Produtos/produtos.php" data-scroll>PRODUTOS</a></li>
        <li class="menu-item"><a href="../Pedidos/pedidos.php" data-scroll>PEDIDOS</a></li>
        <li class="menu-item active"><a href="pagamento.php" data-scroll>PAGAMENTO</a></li>
        <?php
        if ($tipo_cliente == 2) {
          echo "<li class='menu-item'><a href='../Admin/admin.php' data-scroll>ADMIN</a></li>";
        }
        ?>
        <li class="menu-item"><a href="../Login/sair.php" data-scroll>SAIR</a></li>
      </ul>
    </nav>
    </header>

    <section id="home">
      <?php
      $ok = $_GET["senha"] ?? '';

      if ($ok) {
        echo "<p style='color: green;'>Senha alterada com sucesso!</p>";
      }
      ?>

      <br>
      <form method="post" action="atualizar_pagamentos.php">
        <h2>Formas de pagamento</h2>
        <?php
        // Verifica se o tipo de pagamento foi atualizado
        if (isset($_GET['valor'])) {
          echo "<p style='color: green;'> Forma de pagamento atualizada com sucesso! </p>";
        }
        ?>
        <p>Selecione as opções de pagamento disponíveis:</p>
        <input type="checkbox" id="dinheiro" name="dinheiro" <?php if ($dinheiro_cliente)
          echo "checked" ?>>
          <label for="dinheiro">Dinheiro</label><br>
          <input type="checkbox" id="pix" name="pix" <?php if ($pix_cliente)
          echo 'checked' ?>>
          <label for="pix">PIX</label><br>
          <input type="checkbox" id="cartao" name="cartao" <?php if ($cartao_cliente)
          echo 'checked' ?>>
          <label for="cartao">Cartão</label><br>
          <input type="checkbox" id="caderneta" name="caderneta" <?php if ($caderneta_cliente)
          echo 'checked' ?>>
          <label for="caderneta">Caderneta</label><br>

          <input type="submit" value="Salvar">
        </form>
      </section>
      <script>
        function verificaSenhas() {
          var senha = document.getElementById("senha").value;
          var confirmar_senha = document.getElementById("confirmar_senha").value;

          if (senha != confirmar_senha) {
            alert("As senhas não são iguais!");
            return false;
          }

          return true;
        }
      </script>

      <script src="../../js/fastclick.js"></script>
      <script src="../../js/scroll.js"></script>
      <script src="../../js/fixed-responsive-nav.js"></script>
  </body>


  </html>