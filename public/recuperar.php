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
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .container-recuperar {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      padding: 32px 24px;
      max-width: 400px;
      width: 90%;
      text-align: center;
    }
    .container-recuperar h2 {
      color: #007bff;
      margin-bottom: 18px;
    }
    .container-recuperar form {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .container-recuperar input {
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 1em;
    }
    .container-recuperar button {
      padding: 10px;
      border-radius: 5px;
      border: none;
      background: #007bff;
      color: #fff;
      font-size: 1em;
      cursor: pointer;
      transition: background 0.2s;
    }
    .container-recuperar button:hover {
      background: #0056b3;
    }
    .erro-recuperar {
      color: #d00;
      margin-top: 8px;
      font-size: 0.95em;
    }
    @media (max-width: 600px) {
      .container-recuperar {
        padding: 18px 8px;
        font-size: 1em;
      }
    }
  </style>
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