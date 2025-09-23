

<?php
session_start();
$host = "localhost";
$db = "sa_viafacil_db";
$user = "root";
$pass = "root";
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
  <link rel="stylesheet" href="../styles/style.css" />
</head>
<body>
  <div class="login-bg">
    <div class="container-login">
      <div class="logo-login">
        <img src="../assets/logo.PNG" alt="Logo Viafacil" />
      </div>
      <div class="avatar-usuario">
        <img src="../assets/avatar-login.png" alt="Usuário" />
      </div>
      <form class="formulario-login" method="POST" action="">
        <input type="text" name="email" placeholder="usuario" required />
        <input type="password" name="senha" placeholder="senha" required />
        <a href="Esqueceusenha.php" class="link-esqueceu">esqueceu sua senha</a>
        <button type="submit">ENTRAR</button>
        <?php if ($erro) { echo '<div class="erro-login">'.$erro.'</div>'; } ?>
      </form>
      <div class="suporte-link">
        <span class="suporte-icone">&#9432;</span>
        <a href="suporte.php">Entre em contato com o Suporte Técnico.</a>
      </div>
    </div>
  </div>
</body>
</html>
