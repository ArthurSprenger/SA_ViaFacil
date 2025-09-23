

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
  <style>
    /* Base e layout */
    html,body { height:100%; margin:0; font-family: 'Segoe UI', Arial, sans-serif; background: linear-gradient(180deg,#07283d 0%, #0d3b66 100%); }
    .page-center { min-height:100vh; display:flex; align-items:center; justify-content:center; }

    /* Card mais estreito no desktop; responsivo no mobile */
    .login-card { width: min(400px, 95vw); background: linear-gradient(180deg,#163048 0%, #20384f 100%); border-radius:18px; padding:22px 20px; box-shadow: 0 8px 28px rgba(0,0,0,0.45); color:#fff; position:relative; }

    /* Logo de marca responsiva */
    .login-logo { display:block; width: clamp(120px, 20vw, 180px); height:auto; margin: 8px auto 10px; }

    /* Avatar responsivo e proporcional: menor no desktop, maior relativo no mobile */
    .login-card { position: relative; padding-top: 14px; overflow: visible; }
    .avatar-holder { width: clamp(120px, 22vw, 280px); height: clamp(120px, 22vw, 280px); border-radius:50%; background:transparent; margin: 6px auto 8px; display:flex; align-items:center; justify-content:center; box-sizing:border-box; }
    .avatar-holder img { width:100%; height:100%; object-fit:contain; display:block; }

    /* Tipografia e formulário */
    .login-title { text-align:center; font-weight:700; letter-spacing:1px; margin:6px 0 10px; color:#cfe8fb; }
    .form-login { display:flex; flex-direction:column; gap:12px; }
    .input-pill { width:100%; box-sizing:border-box; padding:10px 14px; border-radius:22px; background: transparent; border:2px solid rgba(255,255,255,0.35); color:#fff; font-size:0.95rem; }
    .input-pill::placeholder { color: rgba(255,255,255,0.65); }
    .link-esqueceu { display:block; text-align:center; margin-top:-4px; font-size:0.85rem; color:#93d3ff; text-decoration:none; }
    .link-esqueceu:hover { text-decoration:underline; }
    .btn-entrar { margin-top:6px; background: linear-gradient(180deg,#33c0ff,#00aee6); color:#fff; border:0; padding:10px 16px; border-radius:20px; font-weight:800; font-size:1rem; cursor:pointer; box-shadow:0 6px 12px rgba(0,160,230,0.18); }
    .erro-login { margin-top:8px; background:#ffefef; color:#8b1d1d; padding:8px 10px; border-radius:8px; font-size:0.92rem; }
    .suporte { margin-top:12px; text-align:center; font-size:0.9rem; color:#9ed9ff; }
    .suporte a { color:#9ed9ff; text-decoration:none; }

    /* Pequenos ajustes extras para telas muito pequenas */
    @media (max-width:420px) {
      .login-card { width:92vw; padding:18px; }
    }

    /* Mobile-only: aumentar a logo do usuário (avatar) */
    @media (max-width:480px) {
      .avatar-holder { width: clamp(180px, 50vw, 320px); height: clamp(180px, 50vw, 320px); }
    }
  </style>
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
