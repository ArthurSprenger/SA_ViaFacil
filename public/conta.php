<?php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Conta | Viafácil</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      min-height: 100vh;
    }
    .conta-header {
      display: flex;
      align-items: center;
      gap: 18px;
      padding: 16px 24px;
      background: #007bff;
      color: #fff;
      border-radius: 0 0 16px 16px;
    }
    .logo {
      max-width: 80px;
    }
    .conta-header h1 {
      font-size: 1.5em;
      margin: 0;
    }
    .conta-container {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      max-width: 400px;
      width: 90%;
      margin: 32px auto;
      padding: 32px 24px;
      text-align: center;
    }
    .perfil-title {
      font-size: 1.2em;
      color: #007bff;
      margin-bottom: 18px;
    }
    .conta-form {
      display: flex;
      flex-direction: column;
      gap: 12px;
      align-items: stretch;
    }
    .conta-form label {
      text-align: left;
      color: #333;
      font-size: 1em;
      margin-bottom: 2px;
    }
    .conta-form input {
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 1em;
    }
    .conta-form button {
      padding: 10px;
      border-radius: 5px;
      border: none;
      background: #007bff;
      color: #fff;
      font-size: 1em;
      cursor: pointer;
      transition: background 0.2s;
    }
    .conta-form button:hover {
      background: #0056b3;
    }
    .sidebar-menu {
      position: fixed;
      left: 0;
      top: 0;
      height: 100vh;
      width: 180px;
      background: #007bff;
      color: #fff;
      padding-top: 60px;
      box-shadow: 2px 0 8px rgba(0,0,0,0.08);
      z-index: 100;
    }
    .sidebar-menu ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .sidebar-menu li {
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      gap: 10px;
      padding-left: 18px;
    }
    .sidebar-menu a {
      color: #fff;
      text-decoration: none;
      font-size: 1em;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .sidebar-icon {
      max-width: 28px;
      vertical-align: middle;
    }
    @media (max-width: 900px) {
      .sidebar-menu {
        width: 120px;
        padding-top: 40px;
      }
      .sidebar-menu li {
        gap: 6px;
        padding-left: 8px;
      }
      .sidebar-icon {
        max-width: 20px;
      }
    }
    @media (max-width: 600px) {
      .conta-header {
        flex-direction: column;
        gap: 8px;
        padding: 10px 8px;
      }
      .logo {
        max-width: 50px;
      }
      .conta-container {
        padding: 12px 4px;
      }
      .sidebar-menu {
        width: 80px;
        padding-top: 20px;
      }
      .sidebar-menu li {
        gap: 4px;
        padding-left: 2px;
      }
      .sidebar-icon {
        max-width: 16px;
      }
    }
  </style>
</head>
<body>
  <header class="conta-header">
    <a href="dashboard.php">
      <img src="../assets/logo.PNG" alt="Viafacil" class="logo" />
    </a>
    <h1>Conta</h1>
  </header>
  <main class="conta-container">
    <div class="perfil-title">Perfil</div>
    <form class="conta-form">
      <label for="nome">Nome</label>
      <input type="text" id="nome" placeholder="Seu nome" />
      <label for="email">Email</label>
      <input type="email" id="email" placeholder="Email" />
      <label for="telefone">Telefone</label>
      <input type="tel" id="telefone" placeholder="Telefone" />
      <button type="submit">Mudar informações</button>
    </form>
  </main>
  <nav class="sidebar-menu">
    <ul>
      <li>
        <a href="dashboard.php">
          <img src="../assets/dashboard.png" alt="Dashboard" class="sidebar-icon dashboard-icon" />
          <span>DASHBOARD</span>
        </a>
      </li>
      <li>
        <a href="conta.php">
          <img src="../assets/logo usuario menu.png" alt="Conta" class="sidebar-icon conta-icon" />
          <span>CONTA</span>
        </a>
      </li>
      <li>
        <img src="../assets/configurações.png" alt="Configurações" class="sidebar-icon" />
        <span>CONFIGURAÇÕES</span>
      </li>
      <li>
        <img src="../assets/sair.png" alt="Sair" class="sidebar-icon" />
        <span>SAIR</span>
      </li>
    </ul>
  </nav>
</body>
</html>