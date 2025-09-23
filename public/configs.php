<?php
$host = "localhost";
$db = "sa_viafacil_db";
$user = "root";
$pass = "root";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Falha na conexão: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Configurações | Viafácil</title>
  <style>
    body{ margin:0; padding:0; font-family: Arial, sans-serif; background:#fff; min-height:100vh; }
    .config-header{ position:relative; display:flex; align-items:center; justify-content:center; padding:18px 16px; background:#003366; color:#fff; border-radius:0 0 16px 16px; min-height:72px; }
    .menu-btn{ background:none; border:0; display:flex; flex-direction:column; gap:4px; position:absolute; left:12px; top:50%; transform:translateY(-50%); cursor:pointer; }
    .menu-btn .bar{ width:28px; height:4px; background:#fff; border-radius:2px; }
    .logo-trens{ width:96px; height:auto; display:block; margin:0 auto; }
    .config-title{ font-size:1.9em; font-weight:800; color:#fff; text-align:center; margin-top:8px; }
    .config-container{ padding:18px; }
    .config-list{ max-width:420px; margin:8px auto 40px; }
    .config-item{ display:flex; align-items:center; justify-content:space-between; padding:12px 8px; border-bottom:1px solid rgba(0,0,0,0.06); }
    .config-item label{ font-weight:700; color:#111; }
    .config-icon{ display:flex; align-items:center; gap:10px; }
    /* switch */
    .switch{ position:relative; display:inline-block; width:46px; height:26px; }
    .switch input{ opacity:0; width:0; height:0; }
    .slider{ position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#ccc; transition:.2s; border-radius:26px; }
    .slider:before{ position:absolute; content:''; height:20px; width:20px; left:3px; top:3px; background:white; transition:.2s; border-radius:50%; }
    .switch input:checked + .slider{ background:#43b649; }
    .switch input:checked + .slider:before{ transform:translateX(20px); }
    /* menu-lateral styles */
    .menu-lateral{ position:fixed; left:0; top:0; height:100vh; width:260px; background:#2f2f2f; color:#fff; padding-top:28px; box-shadow:2px 0 12px rgba(0,0,0,0.3); transform:translateX(-110%); transition:transform .28s ease; z-index:1000; }
    .menu-lateral.ativo{ transform:translateX(0); }
    .sobreposicao-menu{ position:fixed; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); opacity:0; visibility:hidden; transition:opacity .2s ease; z-index:900; }
    .sobreposicao-menu.ativo{ opacity:1; visibility:visible; }
    .lista-itens{ list-style:none; padding:0 12px; margin:0; }
    .item-menu{ display:flex; align-items:center; gap:12px; padding:14px 8px; border-radius:8px; color:#fff; cursor:pointer; margin-bottom:8px; }
    .item-menu:hover{ background:rgba(255,255,255,0.04); }
    .item-menu a{ color:inherit; text-decoration:none; display:flex; align-items:center; gap:12px; width:100%; }
    .icone-item{ width:36px; height:36px; display:block; }
    .texto-item{ font-weight:700; font-size:0.95em; }
    @media(max-width:600px){ .logo-trens{ width:80px; } .config-title{ font-size:1.6em; } }
  </style>
</head>
<body>
  <header class="config-header">
    <button class="menu-btn" aria-label="Abrir menu">
      <span class="bar"></span>
      <span class="bar"></span>
      <span class="bar"></span>
    </button>
    <a href="dashboard.php"><img src="../assets/logo.PNG" alt="Viafácil" class="logo-trens"/></a>
    <div class="config-title">CONFIGURAÇÕES</div>
  </header>

  <nav class="menu-lateral" id="menuLateral">
    <ul class="lista-itens">
      <li class="item-menu"><a href="dashboard.php"><img src="../assets/dashboard.png" class="icone-item" alt="Dashboard"/><span class="texto-item">DASHBOARD</span></a></li>
      <li class="item-menu"><a href="conta.php"><img src="../assets/logo usuario menu.png" class="icone-item" alt="Conta"/><span class="texto-item">CONTA</span></a></li>
      <li class="item-menu"><a href="configs.php"><img src="../assets/configurações.png" class="icone-item" alt="Configurações"/><span class="texto-item">CONFIGURAÇÕES</span></a></li>
      <li class="item-menu"><a href="login.php"><img src="../assets/sair.png" class="icone-item" alt="Sair"/><span class="texto-item">SAIR</span></a></li>
    </ul>
  </nav>
  <div class="sobreposicao-menu" id="sobreposicaoMenu"></div>

  <main class="config-container">
    <div class="config-list">
      <div class="config-item">
        <label for="notificacoes">PERMITIR NOTIFICAÇÕES</label>
        <label class="switch">
          <input type="checkbox" id="notificacoes" checked>
          <span class="slider"></span>
        </label>
      </div>
      <div class="config-item">
        <label>IDIOMA</label>
        <div class="config-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M12 3V5M12 19V21M4.22 4.22L5.64 5.64M18.36 18.36L19.78 19.78M1 12H3M21 12H23M4.22 19.78L5.64 18.36M18.36 5.64L19.78 4.22" stroke="#222" stroke-width="2" stroke-linecap="round"/>
            <text x="7" y="17" font-size="7" fill="#222">A*</text>
          </svg>
        </div>
      </div>
      <div class="config-item">
        <label>PRIVACIDADE</label>
        <div class="config-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
            <rect x="5" y="11" width="14" height="8" rx="2" stroke="#222" stroke-width="2"/>
            <path d="M8 11V7a4 4 0 1 1 8 0v4" stroke="#222" stroke-width="2"/>
          </svg>
        </div>
      </div>
      <div class="config-item">
        <label>ATUALIZAÇÕES DO APP</label>
        <div class="config-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
            <path d="M4 4v5h5M20 20v-5h-5" stroke="#222" stroke-width="2" stroke-linecap="round"/>
            <path d="M5 19a9 9 0 1 0 2-13.9" stroke="#222" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
      </div>
    </div>
  </main>

  <script>
    (function(){
      const botaoMenu = document.querySelector('.menu-btn');
      const menuLateral = document.getElementById('menuLateral');
      const sobreposicao = document.getElementById('sobreposicaoMenu');

      function abrirMenu(){ menuLateral.classList.add('ativo'); sobreposicao.classList.add('ativo'); menuLateral.setAttribute('aria-hidden','false'); }
      function fecharMenu(){ menuLateral.classList.remove('ativo'); sobreposicao.classList.remove('ativo'); menuLateral.setAttribute('aria-hidden','true'); }

      botaoMenu.addEventListener('click', function(){ if(menuLateral.classList.contains('ativo')) fecharMenu(); else abrirMenu(); });
      sobreposicao.addEventListener('click', fecharMenu);
      document.addEventListener('keydown', function(e){ if(e.key === 'Escape') fecharMenu(); });
      Array.from(menuLateral.querySelectorAll('a')).forEach(function(link){ link.addEventListener('click', function(){ fecharMenu(); }); });
    })();
  </script>
</body>
</html>