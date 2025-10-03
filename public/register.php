<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php'; // $pdo
require_once __DIR__ . '/../src/User.php';

$erro = '';$ok='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $nome = trim($_POST['nome'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $senha = trim($_POST['password'] ?? '');
  if(!$nome || !$email || !$senha){
    $erro = 'Preencha todos os campos.';
  } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erro = 'E-mail inválido.';
  } else {
    try{
      $userRepo = new User($pdo);
      $userRepo->register($nome,$email,$senha);
      header('Location: login.php');
      exit;
    } catch(Throwable $e){
      $erro = 'Falha ao registrar: ' . $e->getMessage();
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrar</title>
  <link rel="stylesheet" href="../styles/login.css" />
  <style>.login-card{max-width:480px;}</style>
  </head>
<body>
  <div class="page-center">
    <div class="login-card">
      <img src="../assets/logo.PNG" alt="Viafacil" class="login-logo">
      <h2 class="login-title">Criar conta</h2>
      <form class="form-login" method="POST" action="">
        <input class="input-pill" type="text" name="nome" placeholder="Seu nome" required>
        <input class="input-pill" type="email" name="email" placeholder="E-mail" required>
        <input class="input-pill" type="password" name="password" placeholder="Senha" required>
        <button class="btn-entrar" type="submit">Registrar</button>
        <?php if($erro){ echo '<div class="erro-login">'.htmlspecialchars($erro).'</div>'; } ?>
      </form>
      <div class="suporte"><a href="login.php">Já tem conta? Entrar</a></div>
    </div>
  </div>
</body>
</html>
