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
  <title>Passageiros | Viafácil</title>
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
      <h1 class="titulo-pagina">Passageiros</h1>

      <section class="secao-busca">
        <input type="text" id="buscarPassageiro" class="input-busca" placeholder="Buscar passageiro por nome ou ID..." />
      </section>

      <section class="secao-conteudo">
        <div class="card-passageiro">
          <div class="passageiro-header">
            <div class="passageiro-id">#001</div>
            <div class="passageiro-status status-ativo">Ativo</div>
          </div>
          <div class="passageiro-info">
            <h3>João Silva Santos</h3>
            <p><strong>CPF:</strong> 123.456.789-00</p>
            <p><strong>Email:</strong> joao.silva@email.com</p>
            <p><strong>Telefone:</strong> (11) 98765-4321</p>
          </div>
          <div class="passageiro-viagem">
            <p><strong>Última Viagem:</strong> Central → Jardim</p>
            <p><strong>Data:</strong> 13/10/2025 às 14:30</p>
          </div>
        </div>

        <div class="card-passageiro">
          <div class="passageiro-header">
            <div class="passageiro-id">#002</div>
            <div class="passageiro-status status-ativo">Ativo</div>
          </div>
          <div class="passageiro-info">
            <h3>Maria Oliveira Costa</h3>
            <p><strong>CPF:</strong> 987.654.321-00</p>
            <p><strong>Email:</strong> maria.oliveira@email.com</p>
            <p><strong>Telefone:</strong> (11) 91234-5678</p>
          </div>
          <div class="passageiro-viagem">
            <p><strong>Última Viagem:</strong> Vila Nova → Central</p>
            <p><strong>Data:</strong> 13/10/2025 às 16:45</p>
          </div>
        </div>

        <div class="card-passageiro">
          <div class="passageiro-header">
            <div class="passageiro-id">#003</div>
            <div class="passageiro-status status-inativo">Inativo</div>
          </div>
          <div class="passageiro-info">
            <h3>Carlos Eduardo Pereira</h3>
            <p><strong>CPF:</strong> 456.789.123-00</p>
            <p><strong>Email:</strong> carlos.pereira@email.com</p>
            <p><strong>Telefone:</strong> (11) 99876-5432</p>
          </div>
          <div class="passageiro-viagem">
            <p><strong>Última Viagem:</strong> Jardim → Vila Nova</p>
            <p><strong>Data:</strong> 10/10/2025 às 09:15</p>
          </div>
        </div>

        <div class="card-passageiro">
          <div class="passageiro-header">
            <div class="passageiro-id">#004</div>
            <div class="passageiro-status status-ativo">Ativo</div>
          </div>
          <div class="passageiro-info">
            <h3>Ana Paula Rodrigues</h3>
            <p><strong>CPF:</strong> 321.654.987-00</p>
            <p><strong>Email:</strong> ana.rodrigues@email.com</p>
            <p><strong>Telefone:</strong> (11) 97654-3210</p>
          </div>
          <div class="passageiro-viagem">
            <p><strong>Última Viagem:</strong> Central → Vila Nova</p>
            <p><strong>Data:</strong> 14/10/2025 às 08:00</p>
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

      const inputBusca = document.getElementById('buscarPassageiro');
      inputBusca.addEventListener('input', function() {
        const termo = this.value.toLowerCase();
        document.querySelectorAll('.card-passageiro').forEach(card => {
          const texto = card.textContent.toLowerCase();
          card.style.display = texto.includes(termo) ? 'block' : 'none';
        });
      });
    })();
  </script>
</body>
</html>
