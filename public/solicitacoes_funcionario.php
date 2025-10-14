<?php
session_start();
if(!isset($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }
if(isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin') { header('Location: dashboard.php'); exit; }

$mensagem = '';
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo_solicitacao'], $_POST['descricao'])){
  $mensagem = '<div class="msg-sucesso">Solicitação enviada com sucesso!</div>';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Solicitações | Viafácil</title>
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
      <h1 class="titulo-pagina">Nova Solicitação</h1>

      <?= $mensagem ?>

      <section class="secao-conteudo">
        <form method="POST" class="form-solicitacao">
          <div class="form-group">
            <label for="tipo_solicitacao">Tipo de Solicitação</label>
            <select id="tipo_solicitacao" name="tipo_solicitacao" class="form-input" required>
              <option value="">Selecione...</option>
              <option value="manutencao">Manutenção</option>
              <option value="limpeza">Limpeza</option>
              <option value="suporte_tecnico">Suporte Técnico</option>
              <option value="seguranca">Segurança</option>
              <option value="outro">Outro</option>
            </select>
          </div>

          <div class="form-group">
            <label for="local">Local</label>
            <input type="text" id="local" name="local" class="form-input" placeholder="Ex: Estação Central - Plataforma 2" required />
          </div>

          <div class="form-group">
            <label for="prioridade">Prioridade</label>
            <select id="prioridade" name="prioridade" class="form-input" required>
              <option value="baixa">Baixa</option>
              <option value="media" selected>Média</option>
              <option value="alta">Alta</option>
              <option value="urgente">Urgente</option>
            </select>
          </div>

          <div class="form-group">
            <label for="descricao">Descrição Detalhada</label>
            <textarea id="descricao" name="descricao" class="form-textarea" rows="6" placeholder="Descreva o problema ou necessidade..." required></textarea>
          </div>

          <button type="submit" class="btn-enviar">Enviar Solicitação</button>
        </form>
      </section>

      <section class="secao-conteudo" style="margin-top: 32px;">
        <h2 class="subtitulo">Minhas Solicitações</h2>

        <div class="card-solicitacao">
          <div class="solicitacao-header">
            <span class="solicitacao-id">#SOL-001</span>
            <span class="solicitacao-status">Pendente</span>
          </div>
          <h3 class="solicitacao-titulo">Lâmpada queimada - Estação Vila Nova</h3>
          <div class="solicitacao-detalhes">
            <p><strong>Tipo:</strong> Manutenção</p>
            <p><strong>Local:</strong> Estação Vila Nova - Sala de espera</p>
            <p><strong>Prioridade:</strong> Média</p>
            <p><strong>Data:</strong> 14/10/2025 - 09:30</p>
          </div>
          <p class="solicitacao-descricao">Lâmpada do teto da sala de espera queimada. Necessita substituição urgente pois está escuro à noite.</p>
        </div>

        <div class="card-solicitacao">
          <div class="solicitacao-header">
            <span class="solicitacao-id">#SOL-002</span>
            <span class="solicitacao-status">Em Andamento</span>
          </div>
          <h3 class="solicitacao-titulo">Limpeza da plataforma</h3>
          <div class="solicitacao-detalhes">
            <p><strong>Tipo:</strong> Limpeza</p>
            <p><strong>Local:</strong> Estação Jardim - Plataforma 1</p>
            <p><strong>Prioridade:</strong> Alta</p>
            <p><strong>Data:</strong> 13/10/2025 - 14:15</p>
          </div>
          <p class="solicitacao-descricao">Plataforma necessita limpeza urgente devido a derramamento de bebida.</p>
          <p class="solicitacao-atualizacao">Última atualização: Equipe de limpeza a caminho (13/10 - 15:00)</p>
        </div>

        <div class="card-solicitacao">
          <div class="solicitacao-header">
            <span class="solicitacao-id">#SOL-003</span>
            <span class="solicitacao-status">Concluída</span>
          </div>
          <h3 class="solicitacao-titulo">Conserto de catraca</h3>
          <div class="solicitacao-detalhes">
            <p><strong>Tipo:</strong> Suporte Técnico</p>
            <p><strong>Local:</strong> Estação Central - Entrada principal</p>
            <p><strong>Prioridade:</strong> Urgente</p>
            <p><strong>Data:</strong> 12/10/2025 - 08:00</p>
          </div>
          <p class="solicitacao-descricao">Catraca 3 travada, impedindo entrada de passageiros.</p>
          <p class="solicitacao-conclusao">Concluída em: 12/10/2025 - 10:30 por João Técnico</p>
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
