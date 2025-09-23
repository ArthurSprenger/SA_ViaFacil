
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
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 18px 16px;
      background: #003366;
      border-radius: 0 0 16px 16px;
      min-height: 64px;
    }
    .logo {
      width: 160px;
      height: auto;
      display: block;
    }
    .menu-btn {
      background: none;
      border: none;
      display: flex;
      flex-direction: column;
      gap: 4px;
      cursor: pointer;
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      z-index: 10;
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
      width: 100%;
      max-width: 160px;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: box-shadow 0.2s;
      padding: 18px;
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
    /* tornar o conteúdo do card inteiro clicável */
    .card a { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px; color:inherit; text-decoration:none; width:100%; height:100%; }
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
    /* container responsivo para permitir scroll horizontal em telas pequenas */
    .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius:6px; }
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
      padding: 6px 10px;
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
      /* diminuir padding da tabela e botões em mobile para reduzir largura */
      .table-section th, .table-section td { padding: 6px; font-size: 0.95em; }
      .btn-aviso { padding: 6px 8px; font-size: 0.85em; }
      /* ajustes mobile */
      .sidebar-icon {
        max-width: 16px;
      }
    }
    /* Estilos do menu lateral em português (sobreposição) */
    .menu-lateral { position: fixed; left: 0; top: 0; height: 100vh; width: 260px; background: #2f2f2f; color: #fff; padding-top: 28px; box-shadow: 2px 0 12px rgba(0,0,0,0.3); transform: translateX(-110%); transition: transform 0.28s ease; z-index: 1000; }
    .menu-lateral.ativo { transform: translateX(0); }
    .sobreposicao-menu { position: fixed; left: 0; top: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.4); opacity: 0; visibility: hidden; transition: opacity 0.2s ease; z-index: 900; }
    .sobreposicao-menu.ativo { opacity: 1; visibility: visible; }
    .lista-itens { list-style: none; padding: 0 12px; margin: 0; }
    .item-menu { display:flex; align-items:center; gap:12px; padding:14px 8px; border-radius:8px; color:#fff; cursor:pointer; margin-bottom:8px; }
    .item-menu:hover { background: rgba(255,255,255,0.04); }
    .item-menu a { color: inherit; text-decoration: none; display: flex; align-items: center; gap: 12px; width: 100%; }
    .icone-item { width:36px; height:36px; display:block; }
    .texto-item { font-weight:700; font-size:0.95em; }
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
    <!-- Menu lateral (em português) -->
    <nav class="menu-lateral" id="menuLateral">
      <ul class="lista-itens">
        <li class="item-menu"><a href="dashboard.php"><img src="../assets/dashboard.png" class="icone-item" alt="Dashboard"/><span class="texto-item">DASHBOARD</span></a></li>
        <li class="item-menu"><a href="conta.php"><img src="../assets/logo usuario menu.png" class="icone-item" alt="Conta"/><span class="texto-item">CONTA</span></a></li>
        <li class="item-menu"><a href="configs.php"><img src="../assets/configurações.png" class="icone-item" alt="Configurações"/><span class="texto-item">CONFIGURAÇÕES</span></a></li>
        <li class="item-menu"><a href="login.php"><img src="../assets/sair.png" class="icone-item" alt="Sair"/><span class="texto-item">SAIR</span></a></li>
      </ul>
    </nav>
    <div class="sobreposicao-menu" id="sobreposicaoMenu"></div>
    <section class="cards">
      <article class="card" id="passageiros">
        <a href="passageiros.php">
          <img src="../assets/passageiros.png" alt="Ícone Passageiros" />
          <span>passageiros</span>
        </a>
      </article>
      <article class="card" id="trens">
        <a href="trenserotas.php">
          <img src="../assets/trens.png" alt="Ícone Trens e Rotas" />
          <span>trens e rotas</span>
        </a>
      </article>
      <article class="card" id="aviso">
        <a href="suporte.php">
          <img src="../assets/aviso.png" alt="Ícone Aviso" />
          <span>aviso</span>
        </a>
      </article>
      <article class="card" id="solicitacao">
        <a href="solicitacoes.php">
          <img src="../assets/solicitacao.png" alt="Ícone Solicitação" />
          <span>solicitação</span>
        </a>
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
    <div class="table-wrap">
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
    </div>
  </section>

  <script>
    // Script para abrir/fechar o menu lateral (nomes em português)
    (function() {
      const botaoMenu = document.querySelector('.menu-btn');
      const menuLateral = document.getElementById('menuLateral');
      const sobreposicao = document.getElementById('sobreposicaoMenu');

      function abrirMenu() {
        menuLateral.classList.add('ativo');
        sobreposicao.classList.add('ativo');
        // manter foco para acessibilidade
        menuLateral.setAttribute('aria-hidden', 'false');
      }

      function fecharMenu() {
        menuLateral.classList.remove('ativo');
        sobreposicao.classList.remove('ativo');
        menuLateral.setAttribute('aria-hidden', 'true');
      }

      botaoMenu.addEventListener('click', function() {
        if (menuLateral.classList.contains('ativo')) {
          fecharMenu();
        } else {
          abrirMenu();
        }
      });

      sobreposicao.addEventListener('click', function() {
        fecharMenu();
      });

      // fechar com Esc
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          fecharMenu();
        }
      });
    })();
  </script>

</body>
</html>