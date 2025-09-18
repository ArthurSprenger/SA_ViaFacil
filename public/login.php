<?php
session_start();
$host = "localhost";
$db = "sa_viafacil_db";
$user = "root";
$pass = "";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
$erro = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    if (!$email || !$senha) {
        $erro = "Preencha todos os campos.";
    } else {
        $sql = "SELECT * FROM usuarios WHERE email = ? AND senha = MD5(?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $email, $senha);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['tipo'] = $row['tipo'];
            header('Location: dashboard.php');
            exit;
        } else {
            $erro = "Usuário ou senha inválidos.";
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Viafacil - Login</title>
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
    .container-login {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      padding: 32px 24px;
      max-width: 400px;
      width: 90%;
      text-align: center;
    }
    .logo-login img {
      max-width: 120px;
      margin-bottom: 16px;
    }
    .avatar-usuario img {
      max-width: 80px;
      margin-bottom: 16px;
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
    .formulario-login a {
      color: #007bff;
      text-decoration: none;
      font-size: 0.95em;
    }
    .formulario-login a:hover {
      text-decoration: underline;
    }
    .erro-login {
      color: #d00;
      margin-top: 8px;
      font-size: 0.95em;
    }
    @media (max-width: 600px) {
      .container-login {
        padding: 18px 8px;
        font-size: 1em;
      }
      .logo-login img {
        max-width: 80px;
      }
      .avatar-usuario img {
        max-width: 50px;
      }
    }
  </style>
</head>
<body>
  <div class="container-login">
    <div class="logo-login">
      <img src="../assets/logo.PNG" alt="Logo Viafacil" />
    </div>
    <div class="avatar-usuario">
      <img src="../assets/logo usuario.png" alt="Usuário" />
    </div>
    <form class="formulario-login" method="POST" action="">
      <input type="text" name="email" placeholder="e-mail" required />
      <input type="password" name="senha" placeholder="senha" required />
      <a href="Esqueceusenha.php" class="link-esqueceu">esqueceu sua senha</a>
      <button type="submit">Entrar</button>
      <a href="suporte.php" class="link-suporte">Entre em contato com o Suporte Técnico</a>
      <?php if ($erro) { echo '<div class="erro-login">'.$erro.'</div>'; } ?>
    </form>
  </div>
</body>
</html>
