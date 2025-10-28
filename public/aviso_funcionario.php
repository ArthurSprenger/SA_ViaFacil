<?php
session_start();
if(!isset($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }
if(isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin') { header('Location: dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Avisos | Viafácil</title>
  <link rel="stylesheet" href="../styles/funcionario_pages.css" />
</head>
<body>
  <div class="page-wrapper">
    <header class="header-func">
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
        <li class="item-menu"><a href="passageiros_funcionario.php"><img src="../assets/passageiros.png" class="icone-item" alt="Passageiros"/><span class="texto-item">PASSAGEIROS</span></a></li>
        <li class="item-menu"><a href="trenserotas.php"><img src="../assets/trens.png" class="icone-item" alt="Trens e Rotas"/><span class="texto-item">TRENS E ROTAS</span></a></li>
        <li class="item-menu"><a href="aviso_funcionario.php"><img src="../assets/aviso.png" class="icone-item" alt="Aviso"/><span class="texto-item">AVISO</span></a></li>
        <li class="item-menu"><a href="solicitacoes_funcionario.php"><img src="../assets/solicitacao.png" class="icone-item" alt="Solicitação"/><span class="texto-item">SOLICITAÇÃO</span></a></li>
        <li class="item-menu"><a href="dashboard_funcionario.php?logout=1"><img src="../assets/sair.png" class="icone-item" alt="Sair"/><span class="texto-item">SAIR</span></a></li>
      </ul>
    </nav>
    <div class="sobreposicao-menu" id="sobreposicaoMenu"></div>

    <main class="conteudo-principal">
      
      <h1 class="titulo-pagina">Avisos</h1>

      <section class="secao-conteudo">
        <h2 class="subtitulo">Avisos Ativos</h2>

        <div class="card-aviso">
          <div class="aviso-header">
            <span class="aviso-badge">URGENTE</span>
            <span class="aviso-data">14/10/2025</span>
          </div>
          <h3 class="aviso-titulo">Interdição temporária - Estação Central</h3>
          <p class="aviso-texto">A Estação Central estará interditada para manutenção de emergência nos trilhos no dia 15/10/2025 das 06h às 10h.</p>
          <div class="aviso-footer">
            <span>Publicado por: Administrador</span>
          </div>
        </div>

        <div class="card-aviso">
          <div class="aviso-header">
            <span class="aviso-badge">ALERTA</span>
            <span class="aviso-data">13/10/2025</span>
          </div>
          <h3 class="aviso-titulo">Atraso nas viagens da linha Jardim</h3>
          <p class="aviso-texto">Devido às condições climáticas, as viagens da linha Jardim podem sofrer atrasos de até 15 minutos.</p>
          <div class="aviso-footer">
            <span>Publicado por: Maria Oliveira</span>
          </div>
        </div>

        <div class="card-aviso">
          <div class="aviso-header">
            <span class="aviso-badge">INFORMAÇÃO</span>
            <span class="aviso-data">12/10/2025</span>
          </div>
          <h3 class="aviso-titulo">Novos horários disponíveis</h3>
          <p class="aviso-texto">A partir de segunda-feira, novos horários noturnos estarão disponíveis para a linha Vila Nova → Central.</p>
          <div class="aviso-footer">
            <span>Publicado por: Carlos Silva</span>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    (function(){
      const botaoMenu = document.querySelector('.menu-btn');
      const menuLateral = document.getElementById('menuLateral');
      const sobreposicao = document.getElementById('sobreposicaoMenu');

      function abrirMenu(){ menuLateral.classList.add('ativo'); sobreposicao.classList.add('ativo'); }
      function fecharMenu(){ menuLateral.classList.remove('ativo'); sobreposicao.classList.remove('ativo'); }

      botaoMenu.addEventListener('click', () => menuLateral.classList.contains('ativo') ? fecharMenu() : abrirMenu());
      sobreposicao.addEventListener('click', fecharMenu);
      document.addEventListener('keydown', (e) => { if(e.key === 'Escape') fecharMenu(); });
      menuLateral.querySelectorAll('a').forEach(link => link.addEventListener('click', fecharMenu));
    })();
  </script>
</body>
</html>
