<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

$erro = "";
$sucesso = "";
$token = $_GET['token'] ?? '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $novaSenha = trim($_POST['nova_senha'] ?? '');
  $confirmarSenha = trim($_POST['confirmar_senha'] ?? '');
  $tokenPost = trim($_POST['token'] ?? '');
  
  if(!$novaSenha || !$confirmarSenha){
    $erro = "Preencha todos os campos.";
  } else if($novaSenha !== $confirmarSenha){
    $erro = "As senhas não coincidem.";
  } else if(strlen($novaSenha) < 6){
    $erro = "A senha deve ter no mínimo 6 caracteres.";
  } else {
    $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
    $sucesso = "Senha redefinida com sucesso! Faça login com a nova senha.";
    header("refresh:2;url=login.php");
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Redefinir Senha | Viafácil</title>
  <link rel="stylesheet" href="../styles/login.css">
</head>
<body>
  <div class="page-center">
    <div class="login-card" role="main">
      <img src="../assets/logo.PNG" alt="Viafacil" class="login-logo">
      <div class="avatar-holder">
        <img src="../assets/logo usuario.png" alt="Usuário">
      </div>

      <h2 style="text-align:center;color:#cfe8fb;margin:12px 0;font-size:1.2rem;">REDEFINIR SENHA</h2>

      <form class="form-login" method="POST" action="">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <input class="input-pill" type="password" name="nova_senha" placeholder="nova senha" required>
        <input class="input-pill" type="password" name="confirmar_senha" placeholder="confirmar senha" required>
        
        <button class="btn-entrar" type="submit">REDEFINIR</button>
        
        <?php if ($erro) { echo '<div class="erro-login">'.htmlspecialchars($erro).'</div>'; } ?>
        <?php if ($sucesso) { echo '<div class="sucesso-login">'.htmlspecialchars($sucesso).'</div>'; } ?>
      </form>

      <div class="suporte link-cadastro">
        <a href="login.php">Voltar ao login</a>
      </div>
      
      <div class="suporte"><a href="suporte.php">&#9432;&nbsp;Entre em contato com o Suporte Técnico.</a></div>
    </div>
  </div>
</body>
</html>