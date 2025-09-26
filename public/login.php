

<?php
session_start();
require_once __DIR__ . '/../config/db.php';
$conn = db_connect();
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
      // Redireciona conforme tipo de usuário
      if ($row['tipo'] === 'admin') {
        header('Location: dashboard.php');
      } else {
        header('Location: dashboard_funcionario.php');
      }
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
    </div>
  </div>
</body>
</html>
