<?php

header('Refresh: 2; URL=https://chat.google.com/room/AAAAEF4_MnA?cls=7');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Suporte Técnico</title>
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
    .container-redirecionamento {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      padding: 32px 24px;
      max-width: 400px;
      width: 90%;
      text-align: center;
    }
    .container-redirecionamento p {
      color: #007bff;
      font-size: 1.2em;
    }
    @media (max-width: 600px) {
      .container-redirecionamento {
        padding: 18px 8px;
        font-size: 1em;
      }
      .container-redirecionamento p {
        font-size: 1em;
      }
    }
  </style>
</head>
<body>
  <div class="container-redirecionamento">
    <h2>Suporte Técnico</h2>
    <p>Redirecionando para o chat de suporte...</p>
  </div>
</body>
</html>