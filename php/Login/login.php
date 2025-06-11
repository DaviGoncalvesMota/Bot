<!DOCTYPE html>
<html lang="pt">

<head>
  <title>BOT</title>
  <meta charset="UTF-8">
  <meta charset="utf-8">
  <meta name="author" content="Adtile">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="../../css/style.css?v=<?php echo time(); ?>">
   <link rel="icon" type="icon" href="../../icons/bot.ico">
</head>

<body>
  <div class="login-page">
    <div class="form">
      <div style="text-align: center"><img src="../../images/bot.png" height="150" width="150"></div>
      <br>

      <form class="login-form" action='autenticar.php' method="post">
        <input type="email" placeholder="EMAIL" id="email" name="email"/>
        <input type="password" placeholder="SENHA" id="senha" name="senha"/>
        <button>ENTRAR</button>
        <p class="message">Não tenho conta, <a href="../Cadastro/cadastro.php">Clique aqui</a></p>
        <p class="message"><a href="../Senha/esqueciSenha.php">Esqueci minha senha</a></p>
      </form>
    </div>
  </div>
</body>

</html>