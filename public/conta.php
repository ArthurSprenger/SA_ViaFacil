<?php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Conta | Viafácil</title>
  <link rel="stylesheet" href="../styles/style.css" />
</head>
<body>
  <header class="conta-header">
    <!-- Removido o botão dos três risquinhos -->
    <a href="dashboard.html">
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
        <a href="dashboard.html">
          <img src="../assets/dashboard.png" alt="Dashboard" class="sidebar-icon dashboard-icon" />
          <span>DASHBOARD</span>
        </a>
      </li>
      <li>
        <a href="conta.html">
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