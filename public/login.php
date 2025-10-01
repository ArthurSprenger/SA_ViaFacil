

<?php
session_start();
// Autenticação conforme modelo enviado (login_db)
require_once __DIR__ . '/../config/db_auth.php';
$db = auth_db();
$erro = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $senha = trim($_POST['senha'] ?? '');
  if (!$username || !$senha) {
    $erro = "Preencha todos os campos.";
  } else {
    $sql = "SELECT pk, username, senha, cargo FROM usuarios WHERE username = ? AND senha = ? LIMIT 1";
    if ($stmt = $db->prepare($sql)) {
      $stmt->bind_param('ss', $username, $senha);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($row = $result->fetch_assoc()) {
        // Sessões conforme exemplo + compatibilidade com o restante do sistema
        $_SESSION['user_pk']    = (int)$row['pk'];
        $_SESSION['username']   = $row['username'];
        $_SESSION['usuario_id'] = (int)$row['pk']; // compatibilidade
        // Mapa de cargo -> tipo usado no restante do sistema
        $_SESSION['tipo'] = ($row['cargo'] === 'adm') ? 'admin' : 'normal';

        // Redirecionar por perfil
        if ($_SESSION['tipo'] === 'admin') {
          header('Location: dashboard.php');
        } else {
          header('Location: dashboard_funcionario.php');
        }
        exit;
      } else {
        $erro = "Usuário ou senha inválidos.";
      }
      $stmt->close();
    } else {
      $erro = "Erro interno ao autenticar.";
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
  <input class="input-pill" type="text" name="username" placeholder="Usuário" required>
        <input class="input-pill" type="password" name="senha" placeholder="senha" required>
        <a class="link-esqueceu" href="Esqueceusenha.php">esqueceu sua senha</a>
        <button class="btn-entrar" type="submit">ENTRAR</button>
        <?php if ($erro) { echo '<div class="erro-login">'.htmlspecialchars($erro).'</div>'; } ?>
      </form>

      <div class="suporte"><a href="suporte.php">&#9432;&nbsp;Entre em contato com o Suporte Técnico.</a></div>
      <div class="suporte" style="margin-top:8px;text-align:center;">
        <a href="cadastro.php" style="font-size:0.9rem;">Cadastrar novo usuário</a>
      </div>
    </div>
  </div>
</body>
</html>
