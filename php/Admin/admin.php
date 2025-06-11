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


<!DOCTYPE html>
<html lang="pt-br">

<head>

  <title>BOT</title>
  <meta charset="utf-8">


  <meta name="author" content="Adtile">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="../../css/styles.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="adminStyles.css?v=<?php echo time(); ?>" type="text/css">
  <link rel="icon" type="icon" href="../../icons/bot.ico">

  <script src="js/responsive-nav.js"></script>
</head>

<body>
  <header>
    <a href="../../index.php" class="logo" data-scroll>BOTPAINEL</a>
    <nav class="nav-collapse">
      <ul>
        <li class="menu-item"><a href="../../index.php" data-scroll>VENDAS</a></li>
        <li class="menu-item"><a href="../Produtos/produtos.php" data-scroll>PRODUTOS</a></li>
        <li class="menu-item"><a href="../Pedidos/pedidos.php" data-scroll>PEDIDOS</a></li>
        <li class="menu-item "><a href="../Pagamento/pagamento.php" data-scroll>PAGAMENTO</a></li>
        <li class="menu-item active"><a href="admin.php" data-scroll>ADMIN</a></li>
        <li class="menu-item"><a href="../Login/sair.php" data-scroll>SAIR</a></li>
      </ul>
    </nav>
  </header>

  <section style="border-bottom: none;" id="home">
    <form method="post">
      <h1>Buscar Usuário</h1>
      <input type="radio" id="opcao_nome" name="opcao_busca" value="nome">
      <label for="opcao_nome">Buscar por nome:</label>
      <input type="text" id="nome_usuario" name="nome_usuario">
      <br>
      <input type="radio" id="opcao_todos" name="opcao_busca" value="todos">
      <label for="opcao_todos">Listar todos:</label>
      <br>
      <input type="submit" value="Buscar">
    </form>
    <br>
    <?php

    $nome_usuario = $_POST['nome_usuario'] ?? '';
    $opcao_busca = $_POST['opcao_busca'] ?? '';
    ?>

    <?php

    // Formulário para resgatar usuários por nome
    if ($opcao_busca != 'todos') {
      $busca_usuario = "SELECT * FROM login WHERE nome LIKE '%$nome_usuario%'";
      $resultado_busca = mysqli_query(mysql: $conn, query: $busca_usuario);

      while ($dados_usuario = mysqli_fetch_array(result: $resultado_busca)) {
        $id_cliente = $dados_usuario['id'];
        $email_cliente = $dados_usuario['email'];
        $senha_cliente = $dados_usuario['senha'];
        $nome_cliente = $dados_usuario['nome'];
        $tipo_cliente = $dados_usuario['tipo'];
        $status_cliente = $dados_usuario['status'];

        ?>

        <!-- Formulário de habilitar e desabilitar -->
        <form method="post" action="atualiza_status.php">
          <h2>Usuário</h2>
          <p>Nome: <?php echo $nome_cliente; ?></p>
          <p>Email: <?php echo $email_cliente; ?> </p>
          <input type="hidden" name="id" value="<?php echo $id_cliente; ?>">
          <p>Status: <?php echo $status_cliente; ?></p>
          <label>
            <input type="radio" name="status" value="ativo" <?php if ($status_cliente == 'ativo')
              echo 'checked' ?>> Ativar
            </label>
            <label>
              <input type="radio" name="status" value="inativo" <?php if ($status_cliente == 'inativo')
              echo 'checked' ?>>
              Desativar
            </label>
            <input type="submit" value="Salvar">
          </form>
          <br>
        <?php
      }
    }
    ?>

    <!-- Formulário de Listar Todos os Usuários -->
    <?php
    if ($opcao_busca == 'todos') {
      $busca_usuario = "SELECT * FROM login";
      $resultado_busca = mysqli_query(mysql: $conn, query: $busca_usuario);

      while ($dados_usuario = mysqli_fetch_array(result: $resultado_busca)) {
        $id_cliente = $dados_usuario['id'];
        $email_cliente = $dados_usuario['email'];
        $senha_cliente = $dados_usuario['senha'];
        $nome_cliente = $dados_usuario['nome'];
        $tipo_cliente = $dados_usuario['tipo'];
        $status_cliente = $dados_usuario['status'];

        ?>
        <form method="post" action="atualiza_status.php">
          <h2>Usuário</h2>
          <p>Nome: <?php echo $nome_cliente; ?></p>
          <p>Email: <?php echo $email_cliente; ?> </p>
          <p>Status: <?php echo $status_cliente; ?></p>
          <label>
            <input type="radio" name="status" value="ativo" <?php if ($status_cliente == 'ativo')
              echo 'checked' ?>> Ativar
            </label>
            <label>
              <input type="radio" name="status" value="inativo" <?php if ($status_cliente == 'inativo')
              echo 'checked' ?>>
              Desativar
            </label>
            <input type="submit" value="Salvar">
          </form>
          <br>
        <?php
      }
    }
    ?>
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