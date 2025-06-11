<!-- <!DOCTYPE html> -->
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>BOT</title>
  <meta name="author" content="Adtile">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="../../css/style.css?v=<?php echo time(); ?>">
</head>

<body>
  <div class="login-page">
    <div class="form">
      <div style="text-align: center">
        <img src="../../images/bot.png" height="150" width="150">
      </div>
      <br>
      <form class="login-form" onsubmit="return verificaSenhas()" action="senha.php" method="POST">
        <input type="email" placeholder="EMAIL" name="email" id="email" required />
        <input type="password" placeholder="NOVA SENHA" id="senha" name="senha" required />
        <input type="password" placeholder="CONFIRMAR SENHA" id="confirmar_senha" name="confirmar_senha" required />
        <button> Redefinir senha </button>
        <p class="message"><a href="../Login/login.php"> Voltar </a></p>
      </form>

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
    </div>
  </div>
</body>

</html>