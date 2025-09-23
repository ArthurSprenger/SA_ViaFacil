<?php

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Passageiros</title>
    <link rel="stylesheet" href="../styles/style3.css">
    <style>

  body { display: flex; flex-direction: column; min-height: 100vh; margin: 0; padding-bottom: 80px; }
  main { flex: 1 0 auto; }

  .rodape-passageiros {
    position: fixed;
    left: 50%;
    transform: translateX(-50%);
    bottom: 12px;
    height: 48px;
    background: #003366;
    width: 320px;
    max-width: calc(100% - 24px);
    border-radius: 12px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding: 0 20px;
    z-index: 999;
    color: #fff;
    box-shadow: 0 6px 18px rgba(0,0,0,0.18);
  }
  .seta-passageiros {
    background: transparent;
    color: #fff;
    border: none;
    padding: 6px 10px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1.25em;
    display:flex; align-items:center; justify-content:center;
  }
  .seta-passageiros:active { transform: translateY(1px); }
  .numero-pagina-passageiros { font-weight: 800; color: #fff; font-size: 1.05em; letter-spacing: 1px; }

  .menu-lateral { position: fixed; left: 0; top: 0; height: 100vh; width: 260px; background: #2f2f2f; color: #fff; padding-top: 28px; box-shadow: 2px 0 12px rgba(0,0,0,0.3); transform: translateX(-110%); transition: transform 0.28s ease; z-index: 1000; }
  .menu-lateral.ativo { transform: translateX(0); }
  .sobreposicao-menu { position: fixed; left: 0; top: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.4); opacity: 0; visibility: hidden; transition: opacity .2s ease; z-index: 900; }
  .sobreposicao-menu.ativo { opacity: 1; visibility: visible; }
  .lista-itens { list-style: none; padding: 0 12px; margin: 0; }
  .item-menu { display:flex; align-items:center; gap:12px; padding:14px 8px; border-radius:8px; color:#fff; cursor:pointer; margin-bottom:8px; }
  .item-menu:hover { background: rgba(255,255,255,0.04); }
  .item-menu a { color: inherit; text-decoration: none; display:flex; align-items:center; gap:12px; width:100%; }
  .icone-item { width:36px; height:36px; display:block; }
  .texto-item { font-weight:700; font-size:0.95em; }

      .container-msg {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 32px 24px;
        max-width: 400px;
        width: 90%;
        text-align: center;
        margin: 18px auto;
      }
      @media (max-width: 600px) {
        .container-msg { padding: 18px 8px; }
      }

      .menu-btn.icone-menu-passageiros {
        background: #003366;
        border: none;
        width: 44px;
        height: 44px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.18);
      }
      .menu-btn.icone-menu-passageiros:hover { opacity: 0.95; }
      .menu-btn.icone-menu-passageiros div {
        width: 24px !important;
        height: 4px !important;
        margin: 4px 0 !important;
        background: #fff !important;
        border-radius: 3px !important;
        display: block !important;
      }
    </style>
</head>
<body>
  <header class="cabecalho-passageiros">
  <button id="menuBtn" class="menu-btn icone-menu-passageiros" aria-label="Abrir menu">
      <div></div>
      <div></div>
      <div></div>
    </button>
        <a href="dashboard.php">
            <img src="../assets/logo.PNG" alt="VIAFACIL" class="logo-passageiros">
        </a>
    </header>

    <h1 class="titulo-passageiros">PASSAGEIROS</h1>

    <main>
      <div class="lista-passageiros">
        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Amanada teixeira</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">48</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">12:14</span>
          </div>
        </div>

        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Pedro Murf</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">78</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">14:30</span>
          </div>
        </div>

        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Arthur Itinga</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">78</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">12:39</span>
          </div>
        </div>

        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Ewerton Oliveira</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">62</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">20:37</span>
          </div>
        </div>

        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Luiz Correa</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">78</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">14:20</span>
          </div>
        </div>

        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Icaro Botelho</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">24</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">14:30</span>
          </div>
        </div>

        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Luiza bohn</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">38</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">21:45</span>
          </div>
        </div>

        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Bruno Jason</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">27</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">23:50</span>
          </div>
        </div>
      </div>
    </main>

  <nav class="menu-lateral" id="menuLateral" aria-hidden="true">
      <ul class="lista-itens">
        <li class="item-menu"><a href="dashboard.php"><img src="../assets/dashboard.png" class="icone-item" alt="Dashboard"/><span class="texto-item">DASHBOARD</span></a></li>
        <li class="item-menu"><a href="conta.php"><img src="../assets/logo usuario menu.png" class="icone-item" alt="Conta"/><span class="texto-item">CONTA</span></a></li>
        <li class="item-menu"><a href="configs.php"><img src="../assets/configurações.png" class="icone-item" alt="Configurações"/><span class="texto-item">CONFIGURAÇÕES</span></a></li>
        <li class="item-menu"><a href="login.php"><img src="../assets/sair.png" class="icone-item" alt="Sair"/><span class="texto-item">SAIR</span></a></li>
      </ul>
    </nav>
    <div class="sobreposicao-menu" id="sobreposicaoMenu"></div>

    <script>
      (function(){
        const botaoMenu = document.querySelector('.menu-btn');
        const menuLateral = document.getElementById('menuLateral');
        const sobreposicao = document.getElementById('sobreposicaoMenu');
        if(!menuLateral || !sobreposicao) return; 

        function abrirMenu(){ menuLateral.classList.add('ativo'); sobreposicao.classList.add('ativo'); menuLateral.setAttribute('aria-hidden','false'); }
        function fecharMenu(){ menuLateral.classList.remove('ativo'); sobreposicao.classList.remove('ativo'); menuLateral.setAttribute('aria-hidden','true'); }

        if(botaoMenu) botaoMenu.addEventListener('click', function(){ if(menuLateral.classList.contains('ativo')) fecharMenu(); else abrirMenu(); });
        sobreposicao.addEventListener('click', fecharMenu);
        document.addEventListener('keydown', function(e){ if(e.key === 'Escape') fecharMenu(); });
        Array.from(menuLateral.querySelectorAll('a')).forEach(function(link){ link.addEventListener('click', function(){ fecharMenu(); }); });
      })();
    </script>
    <footer class="rodape-passageiros">
        <button class="seta-passageiros" id="prevBtn">&#8592;</button>
        <span class="numero-pagina-passageiros">01</span>
        <button class="seta-passageiros" id="nextBtn">&#8594;</button>
    </footer>
    <script src="../scripts/passageiros.js"></script>
    <script src="../scripts/script.js"></script>
</body>
</html>