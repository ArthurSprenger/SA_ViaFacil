<?php
session_start();
// Segurança: precisa estar logado e ser usuário normal
if(!isset($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }
if(isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin') { header('Location: dashboard.php'); exit; }
// Permitir logout rápido por query (opcional)
if(isset($_GET['logout'])){ session_destroy(); header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Funcionário | Viafácil</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; }
  html, body { max-width: 100%; overflow-x: hidden; }
  html { margin: 0 !important; padding: 0 !important; }
  body { margin:0 !important; padding:0 !important; font-family: Arial, sans-serif; background:#f7f7f7; min-height:100vh; }
  .dashboard-bg { width:100%; background:#003366; box-shadow:0 2px 8px rgba(0,0,0,0.08); border-radius:0 0 16px 16px; padding-bottom:6px; margin-top:0 !important; }
  .header { position:relative; display:flex; align-items:flex-start; justify-content:center; padding:0 14px 0 14px; background:transparent; border-radius:0; min-height:0; margin-top:0 !important; }
  .logo { width:148px; height:auto; display:block; margin-top:-100px; }
  .menu-btn { background:none; border:none; display:flex; flex-direction:column; gap:3px; cursor:pointer; position:absolute; left:14px; top:22px; transform:none; z-index:10; }
    .bar { width:26px; height:3px; background:#fff; border-radius:2px; }
  .cards { display:grid; grid-template-columns: repeat(2, 1fr); gap:8px; padding:0 10px 12px; margin-top:-72px; justify-items:center; background:#003366; max-width:100%; width:100%; }
  .card { background:#e6e6e6; border-radius:16px; box-shadow:0 1px 4px rgba(0,0,0,0.10); width:100%; max-width:none; display:flex; flex-direction:column; align-items:center; transition: box-shadow .2s; padding:8px 8px; }
  .card img { max-width:50px; margin-bottom:6px; }
  .card span { font-size:1.0em; color:#222; font-weight:bold; text-align:center; }
    .card a { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px; color:inherit; text-decoration:none; width:100%; height:100%; }
  .section { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.08); max-width:900px; width:95vw; margin:24px auto; padding:24px 18px; box-sizing: border-box; overflow: hidden; }
    .section h2 { margin:0 0 12px; color:#007bff; font-size:1.3em; }
  .table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; border-radius:6px; max-width:100%; width: 100%; }
  table { width:100%; border-collapse:collapse; table-layout: fixed; min-width: 520px; }
  th, td { border:1px solid #ddd; padding:8px; text-align:center; word-wrap: break-word; overflow-wrap: anywhere; }
  .table-wrap table th:nth-child(5), .table-wrap table td:nth-child(5) { width: 120px; }
    th { background:#f1f1f1; color:#007bff; }
    .btn-primary { background:#43b649; color:#fff; border:none; border-radius:5px; padding:8px 12px; cursor:pointer; font-weight:bold; transition: background .2s; }
    .btn-primary:hover { background:#2e8c34; }
    .menu-lateral { position:fixed; left:0; top:0; height:100vh; width:260px; background:#2f2f2f; color:#fff; padding-top:28px; box-shadow:2px 0 12px rgba(0,0,0,0.3); transform:translateX(-110%); transition:transform .28s ease; z-index:1000; }
    .menu-lateral.ativo { transform:translateX(0); }
    .sobreposicao-menu { position:fixed; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); opacity:0; visibility:hidden; transition:opacity .2s ease; z-index:900; }
    .sobreposicao-menu.ativo { opacity:1; visibility:visible; }
    .lista-itens { list-style:none; padding:0 12px; margin:0; }
    .item-menu { display:flex; align-items:center; gap:12px; padding:14px 8px; border-radius:8px; color:#fff; cursor:pointer; margin-bottom:8px; }
    .item-menu:hover { background:rgba(255,255,255,0.04); }
    .item-menu a { color:inherit; text-decoration:none; display:flex; align-items:center; gap:12px; width:100%; }
    .icone-item { width:36px; height:36px; display:block; }
    .texto-item { font-weight:700; font-size:0.95em; }
  /* Tabela resumo com fade indicando continuação */
  .viagens-resumo { position:relative; }
  .viagens-resumo tbody { display:block; max-height:220px; overflow:hidden; position:relative; }
  .viagens-resumo thead, .viagens-resumo tbody tr { display:table; width:100%; table-layout:fixed; }
  .viagens-resumo tbody::after { content:""; position:absolute; left:0; right:0; bottom:0; height:46px; background:linear-gradient(to bottom, rgba(255,255,255,0), #ffffff 65%); pointer-events:none; }
  .ver-mais-wrapper { margin-top:4px; display:flex; justify-content:center; }
  .ver-mais-seta { background:#003366; color:#fff; width:60px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:28px; text-decoration:none; font-weight:bold; box-shadow:0 2px 6px rgba(0,0,0,0.25); transition:background .2s; }
  .ver-mais-seta:hover { background:#005199; }
  @media (max-width: 900px) { .cards{ gap:10px; padding:0 8px 12px; margin-top:-64px; } }
    @media (max-width: 600px) { 
      .cards{ grid-template-columns:1fr 1fr; gap:8px; padding:0 8px 12px; margin-top:-56px; } 
      .card{ max-width:100%; padding:12px 4px; }
      th, td { padding:6px; font-size: 0.95em; }
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
      <a href="dashboard_funcionario.php">
        <img src="../assets/logo.PNG" alt="Viafacil" class="logo" />
      </a>
    </header>
    <nav class="menu-lateral" id="menuLateral">
      <ul class="lista-itens">
  <li class="item-menu"><a href="dashboard_funcionario.php"><img src="../assets/dashboard.png" class="icone-item" alt="Dashboard"/><span class="texto-item">DASHBOARD</span></a></li>
        <li class="item-menu"><a href="passageiros.php"><img src="../assets/passageiros.png" class="icone-item" alt="Passageiros"/><span class="texto-item">PASSAGEIROS</span></a></li>
        <li class="item-menu"><a href="trenserotas.php"><img src="../assets/trens.png" class="icone-item" alt="Trens e Rotas"/><span class="texto-item">TRENS E ROTAS</span></a></li>
        <li class="item-menu"><a href="aviso.php"><img src="../assets/aviso.png" class="icone-item" alt="Aviso"/><span class="texto-item">AVISO</span></a></li>
        <li class="item-menu"><a href="solicitacoes.php"><img src="../assets/solicitacao.png" class="icone-item" alt="Solicitação"/><span class="texto-item">SOLICITAÇÃO</span></a></li>
  <li class="item-menu"><a href="dashboard_funcionario.php?logout=1"><img src="../assets/sair.png" class="icone-item" alt="Sair"/><span class="texto-item">SAIR</span></a></li>
      </ul>
    </nav>
    <div class="sobreposicao-menu" id="sobreposicaoMenu"></div>
    <section class="cards">
      <article class="card">
        <a href="passageiros.php">
          <img src="../assets/passageiros.png" alt="Ícone Passageiros" />
          <span>passageiros</span>
        </a>
      </article>
      <article class="card">
        <a href="trenserotas.php">
          <img src="../assets/trens.png" alt="Ícone Trens e Rotas" />
          <span>trens e rotas</span>
        </a>
      </article>
      <article class="card">
        <a href="aviso.php">
          <img src="../assets/aviso.png" alt="Ícone Aviso" />
          <span>aviso</span>
        </a>
      </article>
      <article class="card">
        <a href="solicitacoes.php">
          <img src="../assets/solicitacao.png" alt="Ícone Solicitação" />
          <span>solicitação</span>
        </a>
      </article>
    </section>
  </div>

  <section class="section">
    <h2>Próximas Viagens</h2>
    <div class="table-wrap viagens-resumo">
      <table>
        <thead>
          <tr>
            <th>Horários</th>
            <th>Origem</th>
            <th>Destino</th>
            <th>Status</th>
            <th>Telefone<br>do maquinista</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>08:00</td><td>Central</td><td>Jardim</td><td>Embarque</td><td>(11) 99999-0001</td></tr>
          <tr><td>09:30</td><td>Jardim</td><td>Vila Nova</td><td>Em rota</td><td>(11) 99999-0002</td></tr>
          <tr><td>10:15</td><td>Vila Nova</td><td>Central</td><td>Aguardando</td><td>(11) 99999-0003</td></tr>
          <tr><td>11:00</td><td>Central</td><td>Vila Nova</td><td>Embarque</td><td>(11) 99999-0004</td></tr>
          <tr><td>11:45</td><td>Jardim</td><td>Central</td><td>Em rota</td><td>(11) 99999-0005</td></tr>
          <tr><td>12:20</td><td>Vila Nova</td><td>Jardim</td><td>Aguardando</td><td>(11) 99999-0006</td></tr>
          <tr><td>13:05</td><td>Central</td><td>Jardim</td><td>Em rota</td><td>(11) 99999-0007</td></tr>
          <tr><td>13:50</td><td>Jardim</td><td>Vila Nova</td><td>Aguardando</td><td>(11) 99999-0008</td></tr>
        </tbody>
      </table>
    </div>
    <div class="ver-mais-wrapper">
      <a href="viagens_completa.php" class="ver-mais-seta" title="Ver tabela completa" aria-label="Ver tabela completa">&#x25BC;</a>
    </div>
  </section>

  <script>
    (function(){
      const botaoMenu = document.querySelector('.menu-btn');
      const menuLateral = document.getElementById('menuLateral');
      const sobreposicao = document.getElementById('sobreposicaoMenu');

      function abrirMenu(){ menuLateral.classList.add('ativo'); sobreposicao.classList.add('ativo'); menuLateral.setAttribute('aria-hidden','false'); }
      function fecharMenu(){ menuLateral.classList.remove('ativo'); sobreposicao.classList.remove('ativo'); menuLateral.setAttribute('aria-hidden','true'); }

      botaoMenu.addEventListener('click', function(){
        if(menuLateral.classList.contains('ativo')) fecharMenu();
        else abrirMenu();
      });
      sobreposicao.addEventListener('click', fecharMenu);
      document.addEventListener('keydown', function(e){ if(e.key === 'Escape') fecharMenu(); });
      Array.from(menuLateral.querySelectorAll('a')).forEach(function(link){ link.addEventListener('click', function(){ fecharMenu(); }); });
    })();
    // (Resumo) Seta leva para página completa de viagens
  </script>
</body>
</html>
