<?php

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Passageiros</title>
    <link rel="stylesheet" href="../styles/style3.css">
    <link rel="stylesheet" href="../styles/passageiros.css">
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
  <li class="item-menu"><a href="logout.php"><img src="../assets/sair.png" class="icone-item" alt="Sair"/><span class="texto-item">SAIR</span></a></li>
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