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
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <title>BOT</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="../../css/styles.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="produtosStyles.css?v=<?php echo time(); ?>">
  <link rel="icon" type="icon" href="../../icons/bot.ico">
  <script src="../../js/responsive-nav.js"></script>
</head>

<body>
  <header>
    <a href="../../index.php" class="logo" data-scroll>BOTPAINEL</a>
    <nav class="nav-collapse">
      <ul>
        <li class="menu-item"><a href="../../index.php" data-scroll>VENDAS</a></li>
        <li class="menu-item active"><a href="produtos.php" data-scroll>PRODUTOS</a></li>
        <li class="menu-item"><a href="../Pedidos/pedidos.php" data-scroll>PEDIDOS</a></li>
        <li class="menu-item"><a href="../Pagamento/pagamento.php" data-scroll>PAGAMENTO</a></li>
        <?php if ($tipo_cliente == 2): ?>
          <li class='menu-item'><a href='../Admin/admin.php' data-scroll>ADMIN</a></li>
        <?php endif; ?>
        <li class="menu-item"><a href="../Login/sair.php" data-scroll>SAIR</a></li>
      </ul>
    </nav>
  </header>

  <?php
  $busca_produtos = "SELECT * FROM produtos WHERE email_painel = '$email_cliente'";
  $resultado_produtos = mysqli_query(mysql: $conn, query: $busca_produtos);
  $total_produtos = mysqli_num_rows(result: $resultado_produtos);
  ?>

  <section id="home">
    <div class="form-div">
      <h1>Produtos</h1>
      <table>
        <tr>
          <th>Produto</th>
          <th>Descrição</th>
          <th>Valor</th>
          <th>N° Produto</th>
          <th>Ações</th>
        </tr>

        <?php
        $busca_produtos = "SELECT * FROM produtos WHERE email_painel = '$email_cliente'";
        $resultado_produtos = mysqli_query($conn, $busca_produtos);

        while ($dados_produtos = mysqli_fetch_array($resultado_produtos)) {
          $id_produto = $dados_produtos['id'];
          $nome_produto = $dados_produtos['nome'];
          $preco_produto = $dados_produtos['preco'];
          $descricao_produto = $dados_produtos['descricao'];
          $num_produto = $dados_produtos['numero_produto'];
          ?>
          <tr>
            <form method="post" action="actions_produtos.php">
              <td>
                <input type="text" required value="<?php echo $nome_produto; ?>" name="nome" placeholder="Nome"
                  lang="pt-BR">
              </td>
              <td>
                <input type="text" required value="<?php echo $descricao_produto; ?>" name="descricao"
                  placeholder="Descrição" lang="pt-BR">
              </td>
              <td>
                <input type="number" required value="<?php echo $preco_produto; ?>" name="preco" step="0.01" min="0"
                  max="9999" lang="pt-BR">
              </td>
              <td>
                <input type="number" name="numero_produto" placeholder="N° Produto" step="0.01" min="0" max="9999"
                  lang="pt-BR" required value="<?php echo $num_produto; ?>" name="num_produto">
              </td>
              <td class="botoes-acao">
                <input type="hidden" name="id" value="<?php echo $id_produto; ?>">
                <input type="submit" name="editar" value="Editar" />
                <input type="submit" name="excluir" value="Excluir" />
              </td>
            </form>
          </tr>
        <?php } ?>
      </table>
    </div>
  </section>


  <section id="home">
    <div style="text-align: center">
      <form method="post" action="insert_produtos.php">
        <h1> Cadastrar Produto</h1>
        <table>
          <tr>
            <th>Produto</th>
            <th>Descrição</th>
            <th>Valor</th>
            <th>N° Produto</th>
          </tr>
          <tr>
            <td><input type="text" id="nome_produto" name="nome_produto" placeholder="Nome"></td>
            <td><input type="text" id="desc_produto" name="desc_produto" placeholder="Descrição"></td>
            <td><input type="number" id="valor_produto" name="valor_produto" step="0.01" placeholder="Valor"></td>
            <td><input type="number" id="num_produto" name="numero_produto" placeholder="N° Produto"></td>
          </tr>
          <tr>
            <td colspan="4" style="text-align: center;">
              <input type="submit" value="Cadastrar" />
            </td>
          </tr>
        </table>
      </form>
    </div>
  </section>


  <script src="../../../js/fastclick.js"></script>
  <script src="../../../js/scroll.js"></script>
  <script src="../../../js/fixed-responsive-nav.js"></script>

</body>

</html>