

<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/User.php';

$erro = "";
$debug = ""; // Debug temporário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $senha = trim($_POST['senha'] ?? '');
  if (!$email || !$senha) {
    $erro = "Preencha todos os campos.";
  } else {
    $userRepo = new User($pdo);
    $auth = new Auth();
    $user = $userRepo->login($email, $senha);
    
    // Debug: verificar o que está retornando
    $debug = "DEBUG: Tipo retornado = " . gettype($user) . " | ";
    if(is_array($user)){
      $debug .= "Keys: " . implode(", ", array_keys($user)) . " | ";
      if(isset($user['error'])){
        $debug .= "Error: " . $user['error'] . " | ";
      }
      if(isset($user['status'])){
        $debug .= "Status: " . $user['status'] . " | ";
      }
      if(isset($user['email'])){
        $debug .= "Email encontrado: " . $user['email'];
      }
    } else if($user === false){
      $debug .= "Retornou FALSE";
    }
    
    if (is_array($user) && isset($user['error']) && $user['error'] === 'pending') {
      if($user['status'] === 'pendente'){
        $erro = "Sua conta está aguardando aprovação do administrador.";
      } else if($user['status'] === 'rejeitado'){
        $erro = "Sua conta foi rejeitada. Entre em contato com o suporte.";
      }
    } else if ($user && is_array($user) && isset($user['id'])) {
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
  <input class="input-pill" type="text" name="email" placeholder="email" required>
        <input class="input-pill" type="password" name="senha" placeholder="senha" required>
        <a class="link-esqueceu" href="Esqueceusenha.php">esqueceu sua senha</a>
        <button class="btn-entrar" type="submit">ENTRAR</button>
        <?php if ($debug) { echo '<div style="background:#ffffcc;color:#000;padding:8px;border-radius:8px;font-size:11px;margin-top:8px;">'.htmlspecialchars($debug).'</div>'; } ?>
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
