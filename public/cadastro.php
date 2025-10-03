<?php
session_start();
require_once __DIR__ . '/../config/db_auth.php';
$mysqli = auth_db();

if (empty($_SESSION['user_pk'])) {
    header('Location: login.php');
    exit;
}

$register_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $new_user = trim($_POST['new_username'] ?? '');
    $new_pass = trim($_POST['new_password'] ?? '');
    $new_func = ($_POST['new_func'] ?? 'func') === 'adm' ? 'adm' : 'func';
    if ($new_user && $new_pass) {
        $stmt = $mysqli->prepare('INSERT INTO usuarios (username, senha, cargo) VALUES (?,?,?)');
        $stmt->bind_param('sss', $new_user, $new_pass, $new_func);
        if ($stmt->execute()) {
            $register_msg = 'Usuário cadastrado com sucesso!';
        } else {
            $register_msg = 'Erro ao cadastrar novo usuário.';
        }
        $stmt->close();
    } else {
        $register_msg = 'Preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro de Novo Usuário do Sistema</title>
  <link rel="stylesheet" href="../styles/login.css" />
  <style>
    .cadastro-wrapper{max-width:520px;margin:24px auto;padding:16px;background:#fff;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,.08)}
    .cadastro-wrapper h2,.cadastro-wrapper h3{margin:0 0 10px}
    .cadastro-msg{margin:8px 0 12px;color:#003366;font-weight:600}
    .cadastro-form{display:flex;flex-direction:column;gap:10px}
    .cadastro-form input,.cadastro-form select{padding:10px;border-radius:8px;border:1px solid #ccc}
    .cadastro-actions{display:flex;gap:10px;align-items:center;margin-top:8px}
    .btn{background:#003366;color:#fff;border:0;border-radius:8px;padding:10px 16px;font-weight:700;cursor:pointer}
    a.voltar{color:#003366;text-decoration:none}
  </style>
  </head>
<body>
  <div class="cadastro-wrapper">
    <form method="post" class="cadastro-form">
      <h2>Bem-vindo, <?= htmlspecialchars($_SESSION['username'] ?? '') ?>!</h2>
      <h3>Cadastro de Novo Usuário</h3>
      <?php if($register_msg): ?><p class="cadastro-msg"><?= htmlspecialchars($register_msg) ?></p><?php endif; ?>
      <input type="text" name="new_username" placeholder="Novo Usuário" required>
      <input type="password" name="new_password" placeholder="Nova Senha" required>
      <select name="new_func">
        <option value="adm">ADM</option>
        <option value="func" selected>FUNC</option>
      </select>
      <div class="cadastro-actions">
        <button class="btn" type="submit" name="register" value="1">Cadastrar</button>
        <a class="voltar" href="login.php">Voltar</a>
      </div>
    </form>
  </div>
</body>
</html>