<?php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Redefinir Senha</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .forgot-container {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      padding: 32px 24px;
      max-width: 400px;
      width: 90%;
      text-align: center;
    }
    .logo-login {
      max-width: 120px;
      margin-bottom: 16px;
    }
    .avatar-usuario img {
      max-width: 80px;
      margin-bottom: 16px;
    }
    .titulo-redefinir {
      color: #007bff;
      font-size: 1.3em;
      margin-bottom: 18px;
    }
    .form-box {
      margin-top: 12px;
    }
    .formulario-login {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .formulario-login input {
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 1em;
    }
    .formulario-login button {
      padding: 10px;
      border-radius: 5px;
      border: none;
      background: #007bff;
      color: #fff;
      font-size: 1em;
      cursor: pointer;
      transition: background 0.2s;
    }
    .formulario-login button:hover {
      background: #0056b3;
    }
    .suporte-link {
      margin-top: 16px;
      font-size: 0.95em;
    }
    .suporte-link a {
      color: #007bff;
      text-decoration: none;
    }
    .suporte-link a:hover {
      text-decoration: underline;
    }
    @media (max-width: 600px) {
      .forgot-container {
        padding: 18px 8px;
        font-size: 1em;
      }
      .logo-login {
        max-width: 80px;
      }
      .avatar-usuario img {
        max-width: 50px;
      }
    }
  </style>
</head>
<body>
  <div class="forgot-container">
    <img src="../assets/logo.PNG" alt="Viafacil" class="logo-login">
    <h1 class="titulo-redefinir">RECUPERE SUA SENHA</h1>
    <div class="avatar-usuario">
      <img src="../assets/logo usuario.png" alt="Usuário">
    </div>
    <div class="form-box">
      <form id="form-redefinir" class="formulario-login">
        <input type="password" placeholder="digite sua nova senha" required>
        <input type="password" placeholder="confirme sua nova senha" required>
        <button type="submit">Redefinir Senha</button>
      </form>
      <div class="suporte-link">
        <a href="suporte.php">
          <span style="font-size:1.2rem;vertical-align:middle;">&#9432;</span>
          Entre em contato com o Suporte Técnico.
        </a>
      </div>
    </div>
  </div>
</body>
</html>