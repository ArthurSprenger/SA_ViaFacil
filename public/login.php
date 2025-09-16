<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Viafacil - Login</title>
  <link rel="stylesheet" href="../styles/style.css" />
</head>
<body>
  <div class="container-login">
    <div class="logo-login">
      <img src="../assets/logo.PNG" alt="Logo Viafacil" />
    </div>
    <div class="avatar-usuario">
      <img src="../assets/logo usuario.png" alt="Usuário" />
    </div>
    <div class="formulario-login">
      <input type="text" placeholder="usuário" />
      <input type="password" placeholder="senha" />

      <a href="Esqueceusenha.html" class="link-esqueceu">esqueceu sua senha</a>

      <button id="botao-entrar">Entrar</button>

      <a href="suporte.html" class="link-suporte">Entre em contato com o Suporte Técnico</a>
    </div>
  </div>
  <script src="../scripts/login.js"></script>
  <script>
    document.getElementById('forgot-link').addEventListener('click', function(e) {
      e.preventDefault();
      window.open('Esqueceusenha.html', '_blank');
    });
  </script>
</body>
</html>
