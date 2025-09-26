<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!$email) {
        $erro = 'Por favor, preencha o e-mail.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Digite um e-mail válido.';
    } else {
        header('Location: redefinirsenha.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Recuperar Senha</title>
  <link rel="stylesheet" href="../styles/recuperar.css">
</head>
<body>
  <div class="container-recuperar">
    <h2>Recuperar Senha</h2>
    <form method="POST">
      <input type="email" name="email" placeholder="Digite seu e-mail" required />
      <button type="submit">Recuperar</button>
      <?php if (!empty($erro)) echo '<div class="erro-recuperar">'.$erro.'</div>'; ?>
    </form>
  </div>
</body>
</html>