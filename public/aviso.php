<?php
session_start();
require_once __DIR__ . '/../includes/helpers.php';
if(!isset($_SESSION['usuario_id'])){
  header('Location: login.php');
  exit;
}
$dashboardUrl = getDashboardUrl();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avisos | Viafácil</title>
    <link rel="stylesheet" href="../styles/style3.css">
    <link rel="stylesheet" href="../styles/aviso.css">
</head>
<body>
  <header class="cabecalho-passageiros">
    <button id="menuBtn" class="icone-menu-passageiros menu-btn" aria-label="Abrir menu">
      <div></div>
      <div></div>
      <div></div>
    </button>
    <a href="<?php echo htmlspecialchars($dashboardUrl); ?>">
      <img src="../assets/logo.PNG" alt="VIAFACIL" class="logo-passageiros">
    </a>
  </header>

  <main class="solicitacoes-container">
    <h1 class="titulo-solicitacoes">AVISOS</h1>

    <div class="tabela-container">
      <table class="tabela-solicitacoes tabela-aviso">
        <tr>
          <th>AVISO</th>
          <th>HORÁRIO</th>
        </tr>
        <tr>
          <td>INTERDIÇÃO TEMPORÁRIA - LINHA 47</td>
          <td>00:00</td>
        </tr>
        <tr>
          <td>MANUTENÇÃO PROGRAMADA - LINHA 33</td>
          <td>00:00</td>
        </tr>
        <tr>
          <td>OBJETO NA VIA - LINHA 63</td>
          <td>00:00</td>
        </tr>
        <tr>
          <td><input type="text" placeholder="Digite aqui"></td>
          <td><input type="text" placeholder="Digite aqui"></td>
        </tr>
      </table>

      <table class="tabela-solicitacoes tabela-aviso">
        <tr>
          <th>LINHA</th>
          <th>DETALHES</th>
        </tr>
        <tr>
          <td>LINHA 47</td>
          <td>Trecho interditado para inspeção de trilhos.</td>
        </tr>
        <tr>
          <td>LINHA 33</td>
          <td>Manutenção preventiva nos compressores.</td>
        </tr>
        <tr>
          <td>LINHA 63</td>
          <td>Equipe acionada para remoção de objeto.</td>
        </tr>
        <tr>
          <td><input type="text" placeholder="Digite aqui"></td>
          <td><input type="text" placeholder="Digite aqui"></td>
        </tr>
      </table>
    </div>

    <button class="botao-enviar">PUBLICAR AVISO</button>
  </main>

  <nav class="menu-lateral" id="menuLateral" aria-hidden="true">
    <ul class="lista-itens">
      <li class="item-menu"><a href="<?php echo htmlspecialchars($dashboardUrl); ?>"><img src="../assets/dashboard.png" class="icone-item" alt="Dashboard"/><span class="texto-item">DASHBOARD</span></a></li>
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

  <script src="../scripts/script.js"></script>
</body>
</html>
