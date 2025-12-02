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
  <link rel="stylesheet" href="../styles/dashboard_funcionario.css" />
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
      <?php
        require_once __DIR__.'/../includes/db_connect.php';
        $foto = 'default.jpg';
        try{
          $st = $pdo->prepare('SELECT foto_perfil FROM usuarios WHERE id=:id');
          $st->bindParam(':id', $_SESSION['usuario_id']);
          $st->execute();
          $row = $st->fetch();
          if($row && !empty($row['foto_perfil'])) $foto = $row['foto_perfil'];
        }catch(Throwable $e){}
      ?>
      <a href="conta.php" class="user-chip">
        <span class="user-chip__name"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
        <img class="user-chip__avatar" src="../uploads/<?= htmlspecialchars($foto) ?>" alt="Foto" />
      </a>
    </header>
    <nav class="menu-lateral" id="menuLateral">
      <ul class="lista-itens">
        <li class="item-menu"><a href="dashboard_funcionario.php"><img src="../assets/dashboard.png" class="icone-item" alt="Dashboard"/><span class="texto-item">DASHBOARD</span></a></li>
        <li class="item-menu"><a href="conta.php"><img src="../assets/logo usuario menu.png" class="icone-item" alt="Conta"/><span class="texto-item">CONTA</span></a></li>
        <li class="item-menu"><a href="passageiros_funcionario.php"><img src="../assets/passageiros.png" class="icone-item" alt="Passageiros"/><span class="texto-item">PASSAGEIROS</span></a></li>
        <li class="item-menu"><a href="trenserotas.php"><img src="../assets/trens.png" class="icone-item" alt="Trens e Rotas"/><span class="texto-item">TRENS E ROTAS</span></a></li>
        <li class="item-menu"><a href="aviso_funcionario.php"><img src="../assets/aviso.png" class="icone-item" alt="Aviso"/><span class="texto-item">AVISO</span></a></li>
        <li class="item-menu"><a href="solicitacoes_funcionario.php"><img src="../assets/solicitacao.png" class="icone-item" alt="Solicitação"/><span class="texto-item">SOLICITAÇÃO</span></a></li>
        <li class="item-menu"><a href="configs.php"><img src="../assets/configurações.png" class="icone-item" alt="Configurações"/><span class="texto-item">CONFIGURAÇÕES</span></a></li>
        <li class="item-menu"><a href="logout.php"><img src="../assets/sair.png" class="icone-item" alt="Sair"/><span class="texto-item">SAIR</span></a></li>
      </ul>
    </nav>
    <div class="sobreposicao-menu" id="sobreposicaoMenu"></div>
    <section class="cards">
      <article class="card">
        <a href="passageiros_funcionario.php">
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
        <a href="aviso_funcionario.php">
          <img src="../assets/aviso.png" alt="Ícone Aviso" />
          <span>aviso</span>
        </a>
      </article>
      <article class="card">
        <a href="solicitacoes_funcionario.php">
          <img src="../assets/solicitacao.png" alt="Ícone Solicitação" />
          <span>solicitação</span>
        </a>
      </article>
    </section>
  </div>

  <section class="section">
    <h2>próximas viagens</h2>
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
      
      // Debug: verificar se os links dos cards estão funcionando
      document.querySelectorAll('.card a').forEach(function(link){
        link.addEventListener('click', function(e){
          console.log('Link clicado:', this.href);
        });
      });
    })();
  </script>
</body>
</html>
