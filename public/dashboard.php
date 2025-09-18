
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Administrador | Viafácil</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      min-height: 100vh;
    }
    .dashboard-bg {
      width: 100%;
      background: #003366;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      border-radius: 0 0 16px 16px;
      padding-bottom: 24px;
    }
    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 24px;
      background: #003366;
      border-radius: 0 0 16px 16px;
    }
    .logo {
      max-width: 120px;
    }
    .menu-btn {
      background: none;
      border: none;
      display: flex;
      flex-direction: column;
      gap: 4px;
      cursor: pointer;
    }
    .bar {
      width: 28px;
      height: 4px;
      background: #fff;
      border-radius: 2px;
    }
    .cards {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 24px;
      padding: 24px;
      justify-items: center;
      background: #003366;
    }
    .card {
      background: #e6e6e6;
      border-radius: 16px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.10);
      padding: 28px 12px;
      text-align: center;
      width: 100%;
      max-width: 160px;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: box-shadow 0.2s;
    }
    .card img {
      max-width: 64px;
      margin-bottom: 12px;
    }
    .card span {
      font-size: 1.15em;
      color: #222;
      font-weight: bold;
    }
    .form-section {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      max-width: 600px;
      width: 95%;
      margin: 24px auto;
      padding: 24px 18px;
    }
    .form-section h2 {
      margin-top: 0;
      color: #007bff;
      font-size: 1.3em;
    }
    .form-section form {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 12px;
    }
    .form-section input {
      flex: 1 1 120px;
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 1em;
    }
    .form-section button {
      padding: 8px 16px;
      border-radius: 5px;
      border: none;
      background: #43b649;
      color: #fff;
      font-size: 1em;
      cursor: pointer;
      transition: background 0.2s;
    }
    .form-section button:hover {
      background: #2e8c34;
    }
    .table-section {
      width: 100%;
      border-collapse: collapse;
      margin-top: 12px;
    }
    .table-section th, .table-section td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: center;
    }
    .table-section th {
      background: #f1f1f1;
      color: #007bff;
    }
    .btn-aviso {
      background: #43b649;
      color: #fff;
      border: none;
      border-radius: 5px;
      padding: 6px 12px;
      cursor: pointer;
      font-size: 0.95em;
      transition: background 0.2s;
      font-weight: bold;
    }
    .btn-aviso:hover {
      background: #2e8c34;
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
      .cards {
        grid-template-columns: 2fr 2fr;
        gap: 18px;
        padding: 18px;
      }
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
      .header {
        flex-direction: column;
        gap: 8px;
        padding: 10px 8px;
      }
      .logo {
        max-width: 70px;
      }
      .cards {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        padding: 12px;
      }
      .card {
        max-width: 100%;
        padding: 18px 4px;
      }
      .form-section {
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
  <div class="dashboard-bg">
    <header class="header">
      <button class="menu-btn" aria-label="Abrir menu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>
      <a href="dashboard.php">
        <img src="../assets/logo.PNG" alt="Viafacil" class="logo" />
      </a>
    </header>
    <section class="cards">
      <article class="card" id="passageiros">
        <img src="../assets/passageiros.png" alt="Ícone Passageiros" />
        <span>passageiros</span>
      </article>
      <article class="card" id="trens">
        <img src="../assets/trens.png" alt="Ícone Trens e Rotas" />
        <span>trens e rotas</span>
      </article>
      <article class="card" id="aviso">
        <img src="../assets/aviso.png" alt="Ícone Aviso" />
        <span>aviso</span>
      </article>
      <article class="card" id="solicitacao">
        <img src="../assets/solicitacao.png" alt="Ícone Solicitação" />
        <span>solicitação</span>
      </article>
    </section>
  </div>
  <section class="form-section">
    <h2>Enviar avisos</h2>
    <form id="aviso-form">
      <input type="text" id="aviso-input" placeholder="enviar avisos" required />
      <button type="submit" id="aviso-btn">ENVIAR EM AVISOS</button>
    </form>
  </section>
  <section class="form-section">
    <h2>Solicitações</h2>
    <table class="table-section">
      <thead>
        <tr>
          <th>Estação</th>
          <th>Horário</th>
          <th>Situação</th>
          <th>Ação</th>
        </tr>
      </thead>
      <tbody id="tabela-solicitacoes">
        <tr>
          <td>Central</td>
          <td>08:00</td>
          <td>Pendente</td>
          <td><button class="btn-aviso">ENVIAR SOLICITAÇÃO</button></td>
        </tr>
        <tr>
          <td>Jardim</td>
          <td>09:30</td>
          <td>Resolvido</td>
          <td><button class="btn-aviso">ENVIAR SOLICITAÇÃO</button></td>
        </tr>
        <tr>
          <td>Vila Nova</td>
          <td>10:15</td>
          <td>Pendente</td>
          <td><button class="btn-aviso">ENVIAR SOLICITAÇÃO</button></td>
        </tr> 
      </tbody>
    </table>
  </section>
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