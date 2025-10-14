

<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php'; // $pdo
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/User.php';

$erro = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $senha = trim($_POST['senha'] ?? '');
  if (!$email || !$senha) {
    $erro = "Preencha todos os campos.";
  } else {
    $userRepo = new User($pdo);
    $auth = new Auth();
    $user = $userRepo->login($email, $senha);
    if ($user) {
      $auth->loginUser($user);
      if (($user['tipo'] ?? 'normal') === 'admin') {
        header('Location: dashboard.php');
      } else {
        header('Location: dashboard_funcionario.php');
      }
      exit;
    } else {
      $erro = "Usuário ou senha inválidos.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Viafacil - Login</title>
  <link rel="stylesheet" href="../styles/login.css" />
</head>
<body>
  <div class="page-center">
    <div class="login-card" role="main">
      <img src="../assets/logo.PNG" alt="Viafacil" class="login-logo">
      <div class="avatar-holder">
        <img src="../assets/logo usuario.png" alt="Usuário">
      </div>

      <form class="form-login" method="POST" action="">
  <input class="input-pill" type="text" name="email" placeholder="usuario" required>
        <input class="input-pill" type="password" name="senha" placeholder="senha" required>
        <a class="link-esqueceu" href="Esqueceusenha.php">esqueceu sua senha</a>
        <button class="btn-entrar" type="submit">ENTRAR</button>
        <?php if ($erro) { echo '<div class="erro-login">'.htmlspecialchars($erro).'</div>'; } ?>
      </form>

      <div class="suporte"><a href="suporte.php">&#9432;&nbsp;Entre em contato com o Suporte Técnico.</a></div>
      <div class="suporte link-cadastro">
        <a href="cadastro.php">Cadastrar novo usuário</a>
      </div>
    </div>
  </div>
</body>
</html>
