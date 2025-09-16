<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Administrador | Viafácil</title>
  <link rel="stylesheet" href="../styles/style.css" />
  <script src="../scripts/script.js"></script>
</head>
<body>
  <div class="dashboard-bg">
    <header class="header">
      <button class="menu-btn" aria-label="Abrir menu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>
      <a href="dashboard.html">
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