<?php
require_once __DIR__ . '/../config/db.php';
$conn = db_connect();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Configurações | Viafácil</title>
  <link rel="stylesheet" href="../styles/configs.css" />
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
  <li class="item-menu"><a href="logout.php"><img src="../assets/sair.png" class="icone-item" alt="Sair"/><span class="texto-item">SAIR</span></a></li>
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