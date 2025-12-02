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

    <main class="conteudo-principal">
      <h1 class="titulo-pagina">avisos</h1>
      <div style="text-align: center; color: #7f8c8d; font-size: 14px; margin-bottom: 20px;">
        Atualização automática a cada 5 segundos
      </div>

      <section class="secao-conteudo" id="lista-avisos">
        <p style="text-align: center; color: #95a5a6;">Carregando avisos...</p>
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

      const tipoLabels = {
        informativo: 'Informativo',
        alerta: 'Alerta',
        urgente: 'Urgente'
      };

      function escapeHtml(value) {
        if (typeof value !== 'string') return value ?? '';
        return value
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#39;');
      }

      async function carregarAvisos() {
        try {
          const response = await fetch('get_avisos.php');
          const avisos = await response.json();
          
          const container = document.getElementById('lista-avisos');
          
          if (!Array.isArray(avisos)) {
            container.innerHTML = '<p style="text-align: center; color: #e67e22;">Falha ao carregar avisos. Tente novamente.</p>';
            return;
          }

          if (avisos.length === 0) {
            container.innerHTML = '<p style="text-align: center; color: #95a5a6;">Nenhum aviso no momento.</p>';
            return;
          }
          
          container.innerHTML = avisos.map(aviso => `
            <div class="card-aviso ${escapeHtml(aviso.tipo || 'informativo')}">
              <div class="aviso-header">
                <span class="aviso-badge">${escapeHtml(tipoLabels[aviso.tipo] || 'Aviso')}</span>
                <span class="aviso-data">${escapeHtml(aviso.data_formatada)}</span>
              </div>
              <h3 class="aviso-titulo">${escapeHtml(aviso.titulo)}</h3>
              <p class="aviso-texto">${escapeHtml(aviso.mensagem).replace(/\\n/g, '<br>')}</p>
              <div class="aviso-footer">
                <span>${escapeHtml(aviso.autor)}</span>
              </div>
            </div>
          `).join('');
        } catch (error) {
          console.error('Erro ao carregar avisos:', error);
        }
      }

      carregarAvisos();
      setInterval(carregarAvisos, 5000);
    })();
  </script>
</body>
</html>
