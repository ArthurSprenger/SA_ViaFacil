<?php
session_start();
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/solicitacoes_service.php';

if(!isset($_SESSION['usuario_id'])){ header('Location: login.php'); exit; }
if(isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin'){ header('Location: dashboard.php'); exit; }

$conn = db_connect();
ensureSolicitacoesSchema($conn);

$dashboardUrl = getDashboardUrl();
$usuarioId = (int)$_SESSION['usuario_id'];

$mensagem = '';
if(isset($_SESSION['flash_solicitacao_func'])){
  $mensagem = $_SESSION['flash_solicitacao_func'];
  unset($_SESSION['flash_solicitacao_func']);
}

$statusLabels = solicitacaoStatusOptions();
$prioridadeLabels = solicitacaoPrioridadeOptions();

$oldForm = [
  'tipo' => '',
  'local' => '',
  'data_hora' => '',
  'prioridade' => 'media',
  'descricao' => ''
];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $tipo = strtolower(trim($_POST['tipo_solicitacao'] ?? ''));
  $local = trim($_POST['local'] ?? '');
  $prioridade = strtolower(trim($_POST['prioridade'] ?? 'media'));
  $dataHora = trim($_POST['data_hora'] ?? '');
  $descricao = trim($_POST['descricao'] ?? '');

  $oldForm = [
    'tipo' => $tipo,
    'local' => $local,
    'data_hora' => $dataHora,
    'prioridade' => $prioridade,
    'descricao' => $descricao
  ];

  $erros = [];
  if($tipo === ''){ $erros[] = 'Informe o tipo da solicitação.'; }
  if($local === ''){ $erros[] = 'Informe o local.'; }
  if($descricao === ''){ $erros[] = 'Descreva a solicitação.'; }
  if(!isset($prioridadeLabels[$prioridade])){ $prioridade = 'media'; }

  $horario = date('Y-m-d H:i:s');
  if($dataHora !== ''){
    $dt = date_create($dataHora);
    if($dt){
      $horario = $dt->format('Y-m-d H:i:s');
    } else {
      $erros[] = 'Data e hora inválidas.';
    }
  }

  if(count($erros) === 0){
    $status = 'pendente';
    $stmt = $conn->prepare('INSERT INTO solicitacoes (usuario_id, tipo, estacao, horario, descricao, prioridade, status) VALUES (?,?,?,?,?,?,?)');
    $stmt->bind_param('issssss', $usuarioId, $tipo, $local, $horario, $descricao, $prioridade, $status);
    if($stmt->execute()){
      // require_once __DIR__ . '/../includes/mqtt_notificacoes.php';
      // $titulo = 'Nova solicitação';
      // $mensagemNotificacao = sprintf('Solicitação em "%s" (%s) registrada.', $local, $prioridadeLabels[$prioridade]);
      // publicarNotificacao('solicitacao', $titulo, $mensagemNotificacao, $usuarioId);
      $_SESSION['flash_solicitacao_func'] = '<div class="msg-sucesso">Solicitação enviada com sucesso!</div>';
      $stmt->close();
      header('Location: solicitacoes_funcionario.php');
      exit;
    }
    $stmt->close();
    $mensagem = '<div class="msg-erro">Erro ao salvar a solicitação. Tente novamente.</div>';
  } else {
    $mensagem = '<div class="msg-erro">' . htmlspecialchars(implode(' ', $erros)) . '</div>';
  }
}

$minhasSolicitacoes = [];
$stmtMinhas = $conn->prepare('SELECT * FROM solicitacoes WHERE usuario_id = ? ORDER BY criado_em DESC LIMIT 50');
$stmtMinhas->bind_param('i', $usuarioId);
$stmtMinhas->execute();
$resultMinhas = $stmtMinhas->get_result();
if($resultMinhas){
  while($row = $resultMinhas->fetch_assoc()){
    $minhasSolicitacoes[] = $row;
  }
  $resultMinhas->free();
}
$stmtMinhas->close();
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
  <a href="<?= htmlspecialchars($dashboardUrl) ?>">
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
      <h1 class="titulo-pagina">nova solicitação</h1>

  <?php if($mensagem){ echo $mensagem; } ?>

      <section class="secao-conteudo">
        <form method="POST" class="form-solicitacao">
          <div class="form-group">
            <label for="tipo_solicitacao">Tipo de Solicitação</label>
            <select id="tipo_solicitacao" name="tipo_solicitacao" class="form-input" required>
              <option value="">Selecione...</option>
              <option value="manutencao" <?= $oldForm['tipo']==='manutencao' ? 'selected' : '' ?>>Manutenção</option>
              <option value="limpeza" <?= $oldForm['tipo']==='limpeza' ? 'selected' : '' ?>>Limpeza</option>
              <option value="suporte_tecnico" <?= $oldForm['tipo']==='suporte_tecnico' ? 'selected' : '' ?>>Suporte Técnico</option>
              <option value="seguranca" <?= $oldForm['tipo']==='seguranca' ? 'selected' : '' ?>>Segurança</option>
              <option value="outro" <?= $oldForm['tipo']==='outro' ? 'selected' : '' ?>>Outro</option>
            </select>
          </div>

          <div class="form-group">
            <label for="local">Local</label>
            <input type="text" id="local" name="local" class="form-input" placeholder="Ex: Estação Central - Plataforma 2" value="<?= htmlspecialchars($oldForm['local']) ?>" required />
          </div>

            <div class="form-group">
              <label for="data_hora">Data e hora desejada</label>
            <input type="datetime-local" id="data_hora" name="data_hora" class="form-input" value="<?= htmlspecialchars($oldForm['data_hora']) ?>" />
            </div>

          <div class="form-group">
            <label for="prioridade">Prioridade</label>
            <select id="prioridade" name="prioridade" class="form-input" required>
              <option value="baixa" <?= $oldForm['prioridade']==='baixa' ? 'selected' : '' ?>>Baixa</option>
              <option value="media" <?= $oldForm['prioridade']==='media' ? 'selected' : '' ?>>Média</option>
              <option value="alta" <?= $oldForm['prioridade']==='alta' ? 'selected' : '' ?>>Alta</option>
              <option value="urgente" <?= $oldForm['prioridade']==='urgente' ? 'selected' : '' ?>>Urgente</option>
            </select>
          </div>

          <div class="form-group">
            <label for="descricao">Descrição Detalhada</label>
            <textarea id="descricao" name="descricao" class="form-textarea" rows="6" placeholder="Descreva o problema ou necessidade..." required><?= htmlspecialchars($oldForm['descricao']) ?></textarea>
          </div>

          <button type="submit" class="btn-enviar">Enviar Solicitação</button>
        </form>
      </section>

      <section class="secao-conteudo" style="margin-top: 32px;">
        <h2 class="subtitulo">Minhas Solicitações</h2>

        <?php if(count($minhasSolicitacoes) === 0): ?>
          <p style="color:#555;">Você ainda não possui solicitações registradas.</p>
        <?php else: ?>
          <?php foreach($minhasSolicitacoes as $sol): ?>
            <?php
              $statusCodigo = strtolower($sol['status'] ?? 'pendente');
              $statusTexto = $statusLabels[$statusCodigo] ?? ucfirst(str_replace('_',' ', $statusCodigo));
              $classeStatus = str_replace('_', '-', $statusCodigo);
              $prioridadeCodigo = strtolower($sol['prioridade'] ?? 'media');
              $prioridadeTexto = $prioridadeLabels[$prioridadeCodigo] ?? ucfirst($prioridadeCodigo);
              $dtCriado = !empty($sol['criado_em']) ? date_create($sol['criado_em']) : null;
              $dtAtualizado = !empty($sol['atualizado_em']) ? date_create($sol['atualizado_em']) : null;
              $dtHorario = !empty($sol['horario']) ? date_create($sol['horario']) : null;
              $horarioTexto = $dtHorario ? $dtHorario->format('d/m/Y H:i') : ($dtCriado ? $dtCriado->format('d/m/Y H:i') : '--');
              $atualizadoTexto = $dtAtualizado ? $dtAtualizado->format('d/m/Y H:i') : ($dtCriado ? $dtCriado->format('d/m/Y H:i') : '--');
            ?>
            <div class="card-solicitacao <?= htmlspecialchars($classeStatus) ?>">
              <div class="solicitacao-header">
                <span class="solicitacao-id">#SOL-<?= str_pad((string)$sol['id'], 4, '0', STR_PAD_LEFT) ?></span>
                <span class="solicitacao-status"><?= htmlspecialchars($statusTexto) ?></span>
              </div>
              <h3 class="solicitacao-titulo"><?= htmlspecialchars(ucwords(str_replace('_',' ', $sol['tipo'] ?? 'Solicitação'))) ?></h3>
              <div class="solicitacao-detalhes">
                <p><strong>Local:</strong> <?= htmlspecialchars($sol['estacao'] ?? '--') ?></p>
                <p><strong>Prioridade:</strong> <?= htmlspecialchars($prioridadeTexto) ?></p>
                <p><strong>Data/Hora:</strong> <?= $horarioTexto ?></p>
              </div>
              <?php if(!empty($sol['descricao'])): ?>
                <p class="solicitacao-descricao"><?= htmlspecialchars($sol['descricao']) ?></p>
              <?php endif; ?>
              <p class="solicitacao-atualizacao">Última atualização: <?= $atualizadoTexto ?></p>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
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
  <?php $conn->close(); ?>
</body>
</html>
