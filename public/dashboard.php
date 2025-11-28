
<?php
session_start();
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../includes/solicitacoes_service.php';
require_once __DIR__.'/../includes/avisos_service.php';
$conn = db_connect();
ensureSolicitacoesSchema($conn);
ensureAvisosSchema($conn);
$tipoSessao = $_SESSION['tipo'] ?? 'normal';
$isAdminSessao = $tipoSessao === 'admin';
$avisoTipos = avisosTipoOptions();
$avisoDestinos = avisosDestinoOptions();
$avisoStatusLabels = avisosStatusOptions();
  if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['__acao']) && $_POST['__acao']==='atualizar_solicitacao'){
    if(!isset($_SESSION['tipo']) || $_SESSION['tipo']!=='admin'){
      header('Location: dashboard_funcionario.php');
      exit;
    }
  $solicitacaoId = (int)($_POST['solicitacao_id'] ?? 0);
  $novoStatus = strtolower(trim($_POST['nova_situacao'] ?? ''));
  $publicarAviso = !empty($_POST['publicar_aviso']);
    $mapStatus = solicitacaoStatusOptions();

    if(!$solicitacaoId || !isset($mapStatus[$novoStatus])){
      flash('flash_solicitacao','<div class="msg-alerta">Seleção inválida para atualização.</div>');
      header('Location: dashboard.php#solicitacoes');exit;
    }

  $stmtInfo = $conn->prepare('SELECT usuario_id, estacao, status, descricao, tipo, prioridade FROM solicitacoes WHERE id=? LIMIT 1');
    $stmtInfo->bind_param('i', $solicitacaoId);
    $stmtInfo->execute();
    $stmtInfo->store_result();
    if($stmtInfo->num_rows === 0){
      flash('flash_solicitacao','<div class="msg-alerta">Solicitação não encontrada.</div>');
      $stmtInfo->close();
      header('Location: dashboard.php#solicitacoes');exit;
    }

    $stmtInfo->bind_result($usuarioSolicitante, $estacaoSolicitada, $statusAnterior, $descricaoSolicitacao, $tipoSolicitacao, $prioridadeSolicitacao);
    $stmtInfo->fetch();
    $stmtInfo->close();

    $stmtUpdate = $conn->prepare('UPDATE solicitacoes SET status=? WHERE id=?');
    $stmtUpdate->bind_param('si', $novoStatus, $solicitacaoId);
    if($stmtUpdate->execute()){
      // require_once __DIR__ . '/../includes/mqtt_notificacoes.php';
      // $titulo = 'Atualização de solicitação';
      // $mensagem = sprintf('Status da solicitação em "%s" alterado para %s.', $estacaoSolicitada, $mapStatus[$novoStatus]);
      // publicarNotificacao('solicitacao', $titulo, $mensagem, $_SESSION['usuario_id'], (int)$usuarioSolicitante);

      $gerouAviso = false;
      $prioridadeSolicitacao = $prioridadeSolicitacao ?: 'media';

      if ($publicarAviso) {
        $estacaoTitulo = $estacaoSolicitada ? trim($estacaoSolicitada) : 'Estação não informada';
        $statusRotulo = $mapStatus[$novoStatus] ?? ucfirst($novoStatus);
        $tituloAviso = sprintf('Solicitação %s - %s', $statusRotulo, $estacaoTitulo);
        $tipoFormatado = $tipoSolicitacao ? ucwords(str_replace('_', ' ', $tipoSolicitacao)) : 'Geral';
        $prioridadeFormatada = $prioridadeSolicitacao ? ucwords($prioridadeSolicitacao) : 'Média';
        $descricaoAviso = $descricaoSolicitacao ?: 'Sem detalhes adicionais.';

        $mensagemAviso = sprintf(
          "Estação: %s\nTipo: %s\nPrioridade: %s\nDescrição: %s",
          $estacaoTitulo,
          $tipoFormatado,
          $prioridadeFormatada,
          $descricaoAviso
        );

        $tipoAvisoAuto = 'informativo';
        if ($prioridadeSolicitacao === 'urgente') {
          $tipoAvisoAuto = 'urgente';
        } elseif ($prioridadeSolicitacao === 'alta') {
          $tipoAvisoAuto = 'alerta';
        }

        $destinoAvisoAuto = 'funcionarios';
        $statusAvisoAuto = 'ativo';

        $avisoExistenteId = null;
        $stmtAvisoBusca = $conn->prepare('SELECT id FROM avisos WHERE solicitacao_id=? LIMIT 1');
        $stmtAvisoBusca->bind_param('i', $solicitacaoId);
        if ($stmtAvisoBusca->execute()) {
          $stmtAvisoBusca->bind_result($avisoExistenteId);
          $stmtAvisoBusca->fetch();
        }
        $stmtAvisoBusca->close();

        if ($avisoExistenteId) {
          $stmtAvisoAtualiza = $conn->prepare('UPDATE avisos SET titulo=?, mensagem=?, tipo=?, destino=?, status=?, expira_em=NULL, encerrado_em=NULL, usuario_id=?, atualizado_em=NOW() WHERE id=?');
          $stmtAvisoAtualiza->bind_param('sssssii', $tituloAviso, $mensagemAviso, $tipoAvisoAuto, $destinoAvisoAuto, $statusAvisoAuto, $_SESSION['usuario_id'], $avisoExistenteId);
          if ($stmtAvisoAtualiza->execute()) {
            $gerouAviso = true;
          }
          $stmtAvisoAtualiza->close();
        } else {
          $stmtAvisoNovo = $conn->prepare('INSERT INTO avisos (titulo, mensagem, tipo, destino, status, expira_em, usuario_id, solicitacao_id) VALUES (?, ?, ?, ?, ?, NULL, ?, ?)');
          $stmtAvisoNovo->bind_param('sssssii', $tituloAviso, $mensagemAviso, $tipoAvisoAuto, $destinoAvisoAuto, $statusAvisoAuto, $_SESSION['usuario_id'], $solicitacaoId);
          if ($stmtAvisoNovo->execute()) {
            $gerouAviso = true;
            $avisoExistenteId = $stmtAvisoNovo->insert_id;
          }
          $stmtAvisoNovo->close();
        }

        if ($gerouAviso) {
          // publicarNotificacao('aviso', $tituloAviso, $mensagemAviso, $_SESSION['usuario_id'], null, [
          //   'persisted' => true,
          //   'tipo_aviso' => $tipoAvisoAuto,
          //   'destino' => $destinoAvisoAuto,
          //   'status' => $statusAvisoAuto,
          //   'solicitacao_id' => $solicitacaoId,
          //   'prioridade' => $prioridadeSolicitacao
          // ]);
        }
      }

      $msgSucesso = '<div class="msg-sucesso">Status atualizado com sucesso.';
      if (!empty($gerouAviso)) {
        $msgSucesso .= ' Aviso publicado para os funcionários.';
      } elseif ($publicarAviso) {
        $msgSucesso .= ' Não foi possível publicar o aviso.';
      }
      $msgSucesso .= '</div>';
      flash('flash_solicitacao', $msgSucesso);
    } else {
      flash('flash_solicitacao','<div class="msg-erro">Falha ao atualizar a solicitação.</div>');
    }
    $stmtUpdate->close();
    header('Location: dashboard.php#solicitacoes');exit;
  }
// Segurança básica: redireciona para login se não autenticado
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

// Carregar usuários para listagem (nome/email)
$usuarios = [];
$resUsers = $conn->query("SELECT id, nome, email, tipo FROM usuarios ORDER BY id ASC LIMIT 200");
if ($resUsers) { while($r = $resUsers->fetch_assoc()) { $usuarios[] = $r; } }

// Mensagens para criação de usuário
$msgUserAdd = '';$msgUserEdit='';
foreach(['flash_user_add'=>'msgUserAdd','flash_user_edit'=>'msgUserEdit'] as $flash=>$var){
  if(isset($_SESSION[$flash])){ ${$var} = $_SESSION[$flash]; unset($_SESSION[$flash]); }
}

$msgSolicitacao = '';
if(isset($_SESSION['flash_solicitacao'])){
  $msgSolicitacao = $_SESSION['flash_solicitacao'];
  unset($_SESSION['flash_solicitacao']);
}

$msgAviso = '';
if(isset($_SESSION['flash_aviso'])){
  $msgAviso = $_SESSION['flash_aviso'];
  unset($_SESSION['flash_aviso']);
}

// Utilitários de segurança
function isAdmin(){ return isset($_SESSION['tipo']) && $_SESSION['tipo']==='admin'; }
function flash($key,$html){ $_SESSION[$key]=$html; }

if ($isAdminSessao && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['__acao'])) {
  $acaoAviso = $_POST['__acao'];
  if ($acaoAviso === 'criar_aviso_solicitacao') {
    $solicitacaoIdAviso = (int)($_POST['solicitacao_id'] ?? 0);
    $tituloManual = trim($_POST['titulo_aviso'] ?? '');
    $mensagemManual = trim($_POST['mensagem_aviso'] ?? '');
    $tipoManual = $_POST['tipo_aviso'] ?? 'informativo';
    $destinoManual = $_POST['destino_aviso'] ?? 'funcionarios';
    $expiraManual = trim($_POST['expira_em'] ?? '');

    if (!$solicitacaoIdAviso) {
      flash('flash_solicitacao', '<div class="msg-erro">Solicitação inválida para criar aviso.</div>');
      header('Location: dashboard.php#solicitacoes');
      exit;
    }

    if (!isset($avisoTipos[$tipoManual])) {
      $tipoManual = 'informativo';
    }
    if (!isset($avisoDestinos[$destinoManual])) {
      $destinoManual = 'funcionarios';
    }

    $stmtSolInfo = $conn->prepare('SELECT s.usuario_id, s.estacao, s.descricao, s.tipo, s.prioridade, s.status, u.nome FROM solicitacoes s INNER JOIN usuarios u ON s.usuario_id = u.id WHERE s.id = ? LIMIT 1');
    $stmtSolInfo->bind_param('i', $solicitacaoIdAviso);
    $stmtSolInfo->execute();
    $stmtSolInfo->store_result();
    if ($stmtSolInfo->num_rows === 0) {
      $stmtSolInfo->close();
      flash('flash_solicitacao', '<div class="msg-erro">Não foi possível localizar a solicitação selecionada.</div>');
      header('Location: dashboard.php#solicitacoes');
      exit;
    }
    $stmtSolInfo->bind_result($dadosUsuarioId, $dadosEstacao, $dadosDescricao, $dadosTipo, $dadosPrioridade, $dadosStatus, $dadosUsuarioNome);
    $stmtSolInfo->fetch();
    $stmtSolInfo->close();

    $estacaoTitulo = $dadosEstacao ? trim($dadosEstacao) : 'Estação não informada';
    $tipoFormatado = $dadosTipo ? ucwords(str_replace('_', ' ', $dadosTipo)) : 'Geral';
    $prioridadeSolic = $dadosPrioridade ? strtolower($dadosPrioridade) : 'media';
    $prioridadeFormatada = ucwords($prioridadeSolic);
    $descricaoBase = $dadosDescricao ?: 'Sem detalhes adicionais.';

    $tituloSugestao = sprintf('Solicitação resolvida - %s', $estacaoTitulo);
    if ($dadosStatus !== 'resolvido') {
      $tituloSugestao = sprintf('Solicitação em %s', $estacaoTitulo);
    }
    $mensagemSugestao = sprintf(
      "Estação: %s\nTipo: %s\nPrioridade: %s\nDescrição: %s",
      $estacaoTitulo,
      $tipoFormatado,
      $prioridadeFormatada,
      $descricaoBase
    );

    if ($tituloManual === '') {
      $tituloManual = $tituloSugestao;
    }
    if ($mensagemManual === '') {
      $mensagemManual = $mensagemSugestao;
    }

    $expiraSql = null;
    if ($expiraManual !== '') {
      $expiraDt = DateTime::createFromFormat('Y-m-d\TH:i', $expiraManual) ?: DateTime::createFromFormat('Y-m-d H:i:s', $expiraManual);
      if ($expiraDt instanceof DateTime) {
        $expiraSql = $expiraDt->format('Y-m-d H:i:s');
      }
    }

    $statusAviso = 'ativo';
    $avisoExistenteId = null;
    $stmtBuscaAviso = $conn->prepare('SELECT id FROM avisos WHERE solicitacao_id = ? LIMIT 1');
    $stmtBuscaAviso->bind_param('i', $solicitacaoIdAviso);
    if ($stmtBuscaAviso->execute()) {
      $stmtBuscaAviso->bind_result($avisoExistenteId);
      $stmtBuscaAviso->fetch();
    }
    $stmtBuscaAviso->close();

    if ($avisoExistenteId) {
      $stmtAtualizaAviso = $conn->prepare('UPDATE avisos SET titulo=?, mensagem=?, tipo=?, destino=?, status=?, expira_em=?, encerrado_em=NULL, usuario_id=?, atualizado_em=NOW() WHERE id=?');
      $stmtAtualizaAviso->bind_param('ssssssii', $tituloManual, $mensagemManual, $tipoManual, $destinoManual, $statusAviso, $expiraSql, $_SESSION['usuario_id'], $avisoExistenteId);
      $execAviso = $stmtAtualizaAviso->execute();
      $stmtAtualizaAviso->close();
      $avisoProcessadoId = $avisoExistenteId;
    } else {
      $stmtCriaAviso = $conn->prepare('INSERT INTO avisos (titulo, mensagem, tipo, destino, status, expira_em, usuario_id, solicitacao_id) VALUES (?,?,?,?,?,?,?,?)');
      $stmtCriaAviso->bind_param('ssssssii', $tituloManual, $mensagemManual, $tipoManual, $destinoManual, $statusAviso, $expiraSql, $_SESSION['usuario_id'], $solicitacaoIdAviso);
      $execAviso = $stmtCriaAviso->execute();
      $avisoProcessadoId = $stmtCriaAviso->insert_id;
      $stmtCriaAviso->close();
    }

    if (!empty($execAviso)) {
      // require_once __DIR__ . '/../includes/mqtt_notificacoes.php';
      // publicarNotificacao('aviso', $tituloManual, $mensagemManual, $_SESSION['usuario_id'], null, [
      //   'persisted' => true,
      //   'tipo_aviso' => $tipoManual,
      //   'destino' => $destinoManual,
      //   'status' => $statusAviso,
      //   'solicitacao_id' => $solicitacaoIdAviso,
      //   'prioridade' => $prioridadeSolic,
      //   'aviso_id' => $avisoProcessadoId
      // ]);
      flash('flash_solicitacao', '<div class="msg-sucesso">Aviso publicado com base na solicitação selecionada.</div>');
      // if (!empty($dadosUsuarioId)) {
      //   publicarNotificacao('solicitacao', 'Solicitação divulgada', 'Sua solicitação foi publicada como aviso para a equipe.', $_SESSION['usuario_id'], (int)$dadosUsuarioId);
      // }
    } else {
      flash('flash_solicitacao', '<div class="msg-erro">Não foi possível publicar o aviso. Tente novamente.</div>');
    }

    header('Location: dashboard.php#solicitacoes');
    exit;
  } elseif (in_array($acaoAviso, ['enviar_aviso', 'atualizar_aviso_status', 'excluir_aviso'], true)) {
    if ($acaoAviso === 'enviar_aviso') {
      $tituloAviso = trim($_POST['titulo_aviso'] ?? '');
      $mensagemAviso = trim($_POST['mensagem_aviso'] ?? '');
      $tipoAviso = $_POST['tipo_aviso'] ?? 'informativo';
      $destinoAviso = $_POST['destino_aviso'] ?? 'todos';
      $expiraBruto = trim($_POST['expira_em'] ?? '');

      if ($tituloAviso === '' || $mensagemAviso === '') {
        flash('flash_aviso','<div class="msg-erro">Preencha título e mensagem para publicar o aviso.</div>');
        header('Location: dashboard.php#avisos');
        exit;
      }

      if (!isset($avisoTipos[$tipoAviso])) {
        $tipoAviso = 'informativo';
      }
      if (!isset($avisoDestinos[$destinoAviso])) {
        $destinoAviso = 'todos';
      }

      $statusAviso = 'ativo';
      $expiraSql = null;
      if ($expiraBruto !== '') {
        $expiraDt = DateTime::createFromFormat('Y-m-d\TH:i', $expiraBruto) ?: DateTime::createFromFormat('Y-m-d H:i:s', $expiraBruto);
        if ($expiraDt instanceof DateTime) {
          $expiraSql = $expiraDt->format('Y-m-d H:i:s');
        }
      }

      $stmtAviso = $conn->prepare('INSERT INTO avisos (titulo, mensagem, tipo, destino, status, expira_em, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
      $stmtAviso->bind_param('ssssssi', $tituloAviso, $mensagemAviso, $tipoAviso, $destinoAviso, $statusAviso, $expiraSql, $_SESSION['usuario_id']);

      if ($stmtAviso->execute()) {
        // require_once __DIR__ . '/../includes/mqtt_notificacoes.php';
        // publicarNotificacao('aviso', $tituloAviso, $mensagemAviso, $_SESSION['usuario_id'], null, [
        //   'persisted' => true,
        //   'tipo_aviso' => $tipoAviso,
        //   'destino' => $destinoAviso,
        //   'status' => $statusAviso,
        //   'expira_em' => $expiraSql
        // ]);
        flash('flash_aviso','<div class="msg-sucesso">Aviso publicado com sucesso.</div>');
      } else {
        flash('flash_aviso','<div class="msg-erro">Erro ao publicar o aviso.</div>');
      }
      $stmtAviso->close();
      header('Location: dashboard.php#avisos');
      exit;
    }

    if ($acaoAviso === 'atualizar_aviso_status') {
      $avisoId = (int)($_POST['aviso_id'] ?? 0);
      $novoStatus = $_POST['novo_status'] ?? 'ativo';
      if (!$avisoId || !isset($avisoStatusLabels[$novoStatus])) {
        flash('flash_aviso','<div class="msg-alerta">Seleção de status inválida.</div>');
        header('Location: dashboard.php#avisos');
        exit;
      }

      if ($novoStatus === 'encerrado') {
        $stmtStatus = $conn->prepare('UPDATE avisos SET status=?, encerrado_em=NOW() WHERE id=?');
      } else {
        $stmtStatus = $conn->prepare('UPDATE avisos SET status=?, encerrado_em=NULL WHERE id=?');
      }
      $stmtStatus->bind_param('si', $novoStatus, $avisoId);
      if ($stmtStatus->execute() && $stmtStatus->affected_rows >= 0) {
        if ($stmtStatus->affected_rows > 0) {
          flash('flash_aviso','<div class="msg-sucesso">Status do aviso atualizado.</div>');
        } else {
          flash('flash_aviso','<div class="msg-alerta">Nenhuma alteração aplicada ao aviso.</div>');
        }
      } else {
        flash('flash_aviso','<div class="msg-erro">Não foi possível atualizar o status do aviso.</div>');
      }
      $stmtStatus->close();
      header('Location: dashboard.php#avisos');
      exit;
    }

    if ($acaoAviso === 'excluir_aviso') {
      $avisoId = (int)($_POST['aviso_id'] ?? 0);
      if (!$avisoId) {
        flash('flash_aviso','<div class="msg-alerta">Aviso inválido para exclusão.</div>');
        header('Location: dashboard.php#avisos');
        exit;
      }

      $stmtDelete = $conn->prepare('DELETE FROM avisos WHERE id=?');
      $stmtDelete->bind_param('i', $avisoId);
      if ($stmtDelete->execute() && $stmtDelete->affected_rows > 0) {
        flash('flash_aviso','<div class="msg-sucesso">Aviso removido com sucesso.</div>');
      } else {
        flash('flash_aviso','<div class="msg-erro">Falha ao remover o aviso selecionado.</div>');
      }
      $stmtDelete->close();
      header('Location: dashboard.php#avisos');
      exit;
    }
  }
}

// Ações de gerenciamento de usuários (apenas admin)
if(isAdmin()){
  // Criação
  if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['__acao']) && $_POST['__acao']==='criar_usuario'){
    $nomeNovo = trim($_POST['novo_nome'] ?? '');
    $emailNovo = trim($_POST['novo_email'] ?? '');
    $senhaNova = trim($_POST['novo_senha'] ?? '');
    $tipoNovo  = ($_POST['novo_tipo'] ?? 'normal') === 'admin' ? 'admin':'normal';
    if(!$nomeNovo || !$emailNovo || !$senhaNova){
      flash('flash_user_add','<div class="msg-erro">Preencha todos os campos para criar o usuário.</div>');
    } else if(!filter_var($emailNovo, FILTER_VALIDATE_EMAIL)) {
      flash('flash_user_add','<div class="msg-erro">E-mail inválido.</div>');
    } else {
      $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email=? LIMIT 1');
      $stmt->bind_param('s',$emailNovo);$stmt->execute();$stmt->store_result();
      if($stmt->num_rows>0){
        flash('flash_user_add','<div class="msg-alerta">Já existe usuário com esse e-mail.</div>');
      } else {
        $senhaHash = password_hash($senhaNova, PASSWORD_DEFAULT);
        $stmtIns = $conn->prepare('INSERT INTO usuarios (nome,email,senha,tipo) VALUES (?,?,?,?)');
        $stmtIns->bind_param('ssss',$nomeNovo,$emailNovo,$senhaHash,$tipoNovo);
        if($stmtIns->execute()) flash('flash_user_add','<div class="msg-sucesso">Usuário criado com sucesso.</div>');
        else flash('flash_user_add','<div class="msg-erro">Erro ao criar usuário.</div>');
        $stmtIns->close();
      }
      $stmt->close();
    }
    header('Location: dashboard.php#usuarios-listagem');exit;
  }

  // Atualização
  if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['__acao']) && $_POST['__acao']==='atualizar_usuario'){
    $idEdit = (int)($_POST['edit_id'] ?? 0);
    $nome = trim($_POST['edit_nome'] ?? '');
    $email = trim($_POST['edit_email'] ?? '');
    $tipo = ($_POST['edit_tipo'] ?? 'normal') === 'admin' ? 'admin':'normal';
    $senhaNova = trim($_POST['edit_senha'] ?? '');
    if(!$idEdit || !$nome || !$email){
      flash('flash_user_edit','<div class="msg-erro">Campos obrigatórios não preenchidos.</div>');
    } elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){
      flash('flash_user_edit','<div class="msg-erro">E-mail inválido.</div>');
    } else {
      // impedir remover último admin ao mudar tipo
      if($tipo==='normal'){
        $resAdm = $conn->query("SELECT COUNT(*) c FROM usuarios WHERE tipo='admin' AND id<>".$idEdit);
        $c = $resAdm? $resAdm->fetch_assoc()['c']:1;
        $resAdm && $resAdm->close();
        if($c==0){
          flash('flash_user_edit','<div class="msg-alerta">Não é possível rebaixar o único admin.</div>');
          header('Location: dashboard.php?edit_user='.$idEdit.'#usuarios-listagem');exit;
        }
      }
      // Verificar e-mail duplicado
      $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email=? AND id<>? LIMIT 1');
      $stmt->bind_param('si',$email,$idEdit);$stmt->execute();$stmt->store_result();
      if($stmt->num_rows>0){
        flash('flash_user_edit','<div class="msg-alerta">Outro usuário já usa este e-mail.</div>');
      } else {
        if($senhaNova!==''){
          $senhaHash = password_hash($senhaNova, PASSWORD_DEFAULT);
          $stmtUp = $conn->prepare('UPDATE usuarios SET nome=?, email=?, tipo=?, senha=? WHERE id=?');
          $stmtUp->bind_param('ssssi',$nome,$email,$tipo,$senhaHash,$idEdit);
        } else {
          $stmtUp = $conn->prepare('UPDATE usuarios SET nome=?, email=?, tipo=? WHERE id=?');
          $stmtUp->bind_param('sssi',$nome,$email,$tipo,$idEdit);
        }
        if($stmtUp->execute()){
          if($idEdit===$_SESSION['usuario_id']) {
            $tipoAnterior = $_SESSION['tipo'];
            $_SESSION['tipo']=$tipo; // atualizar sessão se alterou próprio tipo
            $_SESSION['username']=$nome; // atualizar nome na sessão
            flash('flash_user_edit','<div class="msg-sucesso">Usuário atualizado.</div>');
            // Se o próprio admin foi rebaixado para normal, mandar para dashboard_funcionario
            if($tipoAnterior==='admin' && $tipo==='normal'){
              header('Location: dashboard_funcionario.php');
              exit;
            }
          } else {
            flash('flash_user_edit','<div class="msg-sucesso">Usuário atualizado.</div>');
          }
        } else flash('flash_user_edit','<div class="msg-erro">Erro ao atualizar.</div>');
        $stmtUp->close();
      }
      $stmt->close();
    }
    header('Location: dashboard.php#usuarios-listagem');exit;
  }

  // Exclusão
  if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['__acao']) && $_POST['__acao']==='excluir_usuario'){
    $idDel = (int)($_POST['del_id'] ?? 0);
    if($idDel && $idDel !== $_SESSION['usuario_id']){
      // Checar se é admin e se é o último
      $resInfo = $conn->query("SELECT tipo FROM usuarios WHERE id=".$idDel);
      if($resInfo && $rowInfo=$resInfo->fetch_assoc()){
        if($rowInfo['tipo']==='admin'){
          $resCount = $conn->query("SELECT COUNT(*) c FROM usuarios WHERE tipo='admin' AND id<>".$idDel);
          $c = $resCount? $resCount->fetch_assoc()['c']:1;
          $resCount && $resCount->close();
          if($c==0){
            flash('flash_user_edit','<div class="msg-alerta">Não é possível excluir o último admin.</div>');
            header('Location: dashboard.php#usuarios-listagem');exit;
          }
        }
        $conn->query("DELETE FROM usuarios WHERE id=".$idDel);
        if($conn->affected_rows>0) flash('flash_user_edit','<div class="msg-sucesso">Usuário removido.</div>');
        else flash('flash_user_edit','<div class="msg-erro">Falha ao remover.</div>');
      }
      $resInfo && $resInfo->close();
    } else {
      flash('flash_user_edit','<div class="msg-alerta">Ação inválida (não pode excluir a si mesmo).</div>');
    }
    header('Location: dashboard.php#usuarios-listagem');exit;
  }
}

// Edição inline agora via JS; remoção do fluxo baseado em GET

// Tratamento de criação de usuário (somente admin)
if(isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin' && $_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['__acao']) && $_POST['__acao']==='criar_usuario'){
  $nomeNovo = trim($_POST['novo_nome'] ?? '');
  $emailNovo = trim($_POST['novo_email'] ?? '');
  $senhaNova = trim($_POST['novo_senha'] ?? '');
  $tipoNovo  = ($_POST['novo_tipo'] ?? 'normal') === 'admin' ? 'admin':'normal';
  if(!$nomeNovo || !$emailNovo || !$senhaNova){
    $_SESSION['flash_user_add'] = '<div class="msg-erro">Preencha todos os campos para criar o usuário.</div>';
  } else if(!filter_var($emailNovo, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_user_add'] = '<div class="msg-erro">E-mail inválido.</div>';
  } else {
    // Verificar duplicidade
    $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email=? LIMIT 1');
    $stmt->bind_param('s',$emailNovo);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows>0){
      $_SESSION['flash_user_add'] = '<div class="msg-alerta">Já existe usuário com esse e-mail.</div>';
    } else {
      $senhaHash = password_hash($senhaNova, PASSWORD_DEFAULT);
      $stmtIns = $conn->prepare('INSERT INTO usuarios (nome,email,senha,tipo) VALUES (?,?,?,?)');
      $stmtIns->bind_param('ssss',$nomeNovo,$emailNovo,$senhaHash,$tipoNovo);
      if($stmtIns->execute()){
        $_SESSION['flash_user_add'] = '<div class="msg-sucesso">Usuário criado com sucesso.</div>';
      } else {
        $_SESSION['flash_user_add'] = '<div class="msg-erro">Erro ao criar usuário.</div>';
      }
      $stmtIns->close();
    }
    $stmt->close();
  }
  header('Location: dashboard.php#usuarios-listagem');
  exit;
}

// Dados básicos de sensores (placeholder)
$sensores = [];
if ($conn->query("SHOW TABLES LIKE 'sensor' ")->num_rows) {
  $resSens = $conn->query("SELECT s.id, s.tipo, s.status, COALESCE(MAX(d.data_hora),'--') AS ultima_leitura, COUNT(d.id) AS total_leituras
                            FROM sensor s
                            LEFT JOIN sensor_data d ON d.id_sensor = s.id
                            GROUP BY s.id, s.tipo, s.status
                            ORDER BY s.id ASC");
  if($resSens){ while($s = $resSens->fetch_assoc()) { $sensores[] = $s; } }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Administrador | Viafácil</title>
  <link rel="stylesheet" href="../styles/dashboard.css" />
</head>
<body>
  <div class="dashboard-bg">
    <header class="header">
      <button class="menu-btn" aria-label="Abrir menu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>
      <a href="dashboard.php">
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
        <li class="item-menu"><a href="dashboard.php"><img src="../assets/dashboard.png" class="icone-item" alt="Dashboard"/><span class="texto-item">DASHBOARD</span></a></li>
        <li class="item-menu"><a href="conta.php"><img src="../assets/logo usuario menu.png" class="icone-item" alt="Conta"/><span class="texto-item">CONTA</span></a></li>
        <li class="item-menu"><a href="configs.php"><img src="../assets/configurações.png" class="icone-item" alt="Configurações"/><span class="texto-item">CONFIGURAÇÕES</span></a></li>
  <li class="item-menu"><a href="logout.php"><img src="../assets/sair.png" class="icone-item" alt="Sair"/><span class="texto-item">SAIR</span></a></li>
      </ul>
    </nav>
    <div class="sobreposicao-menu" id="sobreposicaoMenu"></div>
    <section class="cards">
      <article class="card" id="passageiros">
        <a href="passageiros.php">
          <img src="../assets/passageiros.png" alt="Ícone Passageiros" />
          <span>passageiros</span>
        </a>
      </article>
      <article class="card" id="trens">
        <a href="trenserotas.php">
          <img src="../assets/trens.png" alt="Ícone Trens e Rotas" />
          <span>trens e rotas</span>
        </a>
      </article>
      <article class="card" id="aviso">
        <a href="dashboard.php#avisos">
          <img src="../assets/aviso.png" alt="Ícone Aviso" />
          <span>aviso</span>
        </a>
      </article>
      <article class="card" id="solicitacoes-card">
        <a href="dashboard.php#solicitacoes">
          <img src="../assets/solicitacao.png" alt="Ícone Solicitação" />
          <span>solicitação</span>
        </a>
      </article>
      <article class="card" id="aprovar">
        <a href="aprovar_usuarios.php">
          <img src="../assets/configurações.png" alt="Ícone Aprovar Usuários" />
          <span>aprovar usuários</span>
          <?php
            $stmtPendentes = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE status='pendente'");
            $totalPendentes = $stmtPendentes ? $stmtPendentes->fetch_assoc()['total'] : 0;
            if($totalPendentes > 0): 
          ?>
            <span style="position:absolute;top:8px;right:8px;background:#ff9800;color:#fff;border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:bold;"><?= $totalPendentes ?></span>
          <?php endif; ?>
        </a>
      </article>
      <article class="card" id="sensores">
        <a href="sensores.php">
          <img src="../assets/iot.png" alt="Ícone Sensores IoT" />
          <span>sensores IoT</span>
        </a>
      </article>
    </section>
  </div>
  
  <?php
    $avisosList = [];
    $sqlAvisos = "SELECT a.id, a.titulo, a.mensagem, a.tipo, a.destino, a.status, a.expira_em,
                         DATE_FORMAT(a.criado_em, '%d/%m/%Y %H:%i') AS criado_em_formatado,
                         DATE_FORMAT(a.expira_em, '%d/%m/%Y %H:%i') AS expira_em_formatado,
                         u.nome AS autor
                  FROM avisos a
                  INNER JOIN usuarios u ON a.usuario_id = u.id
                  ORDER BY CASE WHEN a.status='ativo' THEN 0 ELSE 1 END, a.criado_em DESC
                  LIMIT 100";
    $resAvisos = $conn->query($sqlAvisos);
    if($resAvisos){
      while($row = $resAvisos->fetch_assoc()){
        $avisosList[] = $row;
      }
      $resAvisos->free();
    }
  ?>
  <section class="form-section" id="avisos">
    <h2>Avisos</h2>
    <?php if($msgAviso){ echo $msgAviso; } ?>
    <?php if($isAdminSessao): ?>
      <form method="POST" class="aviso-form-grid">
        <input type="hidden" name="__acao" value="enviar_aviso" />
        <div class="aviso-grid">
          <input type="text" name="titulo_aviso" placeholder="Título do aviso" required maxlength="255" />
          <select name="tipo_aviso" required>
            <?php foreach($avisoTipos as $tipoCodigo => $tipoRotulo): ?>
              <option value="<?= htmlspecialchars($tipoCodigo) ?>"><?= htmlspecialchars($tipoRotulo) ?></option>
            <?php endforeach; ?>
          </select>
          <select name="destino_aviso" required>
            <?php foreach($avisoDestinos as $destinoCodigo => $destinoRotulo): ?>
              <option value="<?= htmlspecialchars($destinoCodigo) ?>"><?= htmlspecialchars($destinoRotulo) ?></option>
            <?php endforeach; ?>
          </select>
          <input type="datetime-local" name="expira_em" />
        </div>
        <textarea name="mensagem_aviso" placeholder="Mensagem do aviso..." required rows="3"></textarea>
        <button type="submit" class="btn-aviso">Publicar aviso</button>
      </form>
    <?php endif; ?>
    <div class="table-wrap">
      <table class="table-section tabela-avisos-admin">
        <thead>
          <tr>
            <th>Título</th>
            <th>Categoria</th>
            <th>Destino</th>
            <th>Publicado</th>
            <th>Expira</th>
            <th>Status</th>
            <th>Autor</th>
            <th>Mensagem</th>
            <?php if($isAdminSessao): ?><th>Ações</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if(count($avisosList) > 0): ?>
            <?php foreach($avisosList as $aviso): ?>
              <?php
                $tipoLabel = $avisoTipos[$aviso['tipo']] ?? ucfirst($aviso['tipo']);
                $destinoLabel = $avisoDestinos[$aviso['destino']] ?? ucfirst($aviso['destino']);
                $statusLabel = $avisoStatusLabels[$aviso['status']] ?? ucfirst($aviso['status']);
                $expiraTexto = $aviso['expira_em_formatado'] ?? '--';
                $mensagemExibida = $aviso['mensagem'];
                if(is_string($mensagemExibida)){
                  if(function_exists('mb_strimwidth')){
                    $mensagemExibida = mb_strimwidth($mensagemExibida, 0, 120, strlen($mensagemExibida) > 120 ? '...' : '', 'UTF-8');
                  } elseif(strlen($mensagemExibida) > 120) {
                    $mensagemExibida = substr($mensagemExibida, 0, 117) . '...';
                  }
                }
              ?>
              <tr class="linha-aviso <?= $aviso['status']==='encerrado' ? 'linha-aviso--encerrado' : '' ?>">
                <td><?= htmlspecialchars($aviso['titulo']) ?></td>
                <td><?= htmlspecialchars($tipoLabel) ?></td>
                <td><?= htmlspecialchars($destinoLabel) ?></td>
                <td><?= htmlspecialchars($aviso['criado_em_formatado']) ?></td>
                <td><?= $expiraTexto ? htmlspecialchars($expiraTexto) : '--' ?></td>
                <td><?= htmlspecialchars($statusLabel) ?></td>
                <td><?= htmlspecialchars($aviso['autor']) ?></td>
                <td class="texto-esquerda"><?= htmlspecialchars($mensagemExibida) ?></td>
                <?php if($isAdminSessao): ?>
                  <td class="acoes-aviso">
                    <form method="POST" class="form-acao-aviso">
                      <input type="hidden" name="__acao" value="atualizar_aviso_status" />
                      <input type="hidden" name="aviso_id" value="<?= (int)$aviso['id'] ?>" />
                      <input type="hidden" name="novo_status" value="<?= $aviso['status']==='ativo' ? 'encerrado' : 'ativo' ?>" />
                      <button type="submit" class="btn-aviso btn-status" title="<?= $aviso['status']==='ativo' ? 'Encerrar aviso' : 'Reativar aviso' ?>">
                        <?= $aviso['status']==='ativo' ? 'Encerrar' : 'Reativar' ?>
                      </button>
                    </form>
                    <form method="POST" class="form-acao-aviso" onsubmit="return confirm('Tem certeza que deseja excluir este aviso?');">
                      <input type="hidden" name="__acao" value="excluir_aviso" />
                      <input type="hidden" name="aviso_id" value="<?= (int)$aviso['id'] ?>" />
                      <button type="submit" class="btn-aviso btn-perigo">Excluir</button>
                    </form>
                  </td>
                <?php endif; ?>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="<?= $isAdminSessao ? '9' : '8' ?>" style="text-align:center;color:#6b7280;">Nenhum aviso cadastrado.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
  
  <section class="form-section" id="solicitacoes">
    <h2>Solicitações</h2>
    <?php
    $sqlSolicitacoes = "SELECT s.*, u.nome as usuario_nome FROM solicitacoes s INNER JOIN usuarios u ON s.usuario_id = u.id ORDER BY s.criado_em DESC LIMIT 50";
    $resSolicitacoes = $conn->query($sqlSolicitacoes);
    $solicitacoes = [];
    if($resSolicitacoes){
      while($row = $resSolicitacoes->fetch_assoc()){
        $solicitacoes[] = $row;
      }
      $resSolicitacoes->free();
    }
    $statusLabels = solicitacaoStatusOptions();
    $prioridadeLabels = solicitacaoPrioridadeOptions();
    ?>
    <?php if($msgSolicitacao){ echo $msgSolicitacao; } ?>
    <div class="table-wrap">
      <table class="table-section tabela-solicitacoes-admin">
      <thead>
        <tr>
          <th>Local</th>
          <th>Tipo</th>
          <th>Data/Hora</th>
          <th>Prioridade</th>
          <th>Status</th>
          <th>Usuário</th>
          <th>Atualizado</th>
          <?php if(isAdmin()): ?><th>Ações</th><?php endif; ?>
        </tr>
      </thead>
      <tbody id="tabela-solicitacoes">
        <?php if(count($solicitacoes) > 0): ?>
          <?php foreach($solicitacoes as $sol): ?>
            <?php
              $prioridadeCodigo = strtolower($sol['prioridade'] ?? 'media');
              $prioridadeTexto = $prioridadeLabels[$prioridadeCodigo] ?? ucfirst($prioridadeCodigo);
              $statusCodigo = strtolower($sol['status'] ?? 'pendente');
              $statusTexto = $statusLabels[$statusCodigo] ?? ucfirst($statusCodigo);
              $horarioTexto = '--';
              if(!empty($sol['horario'])){
                $dtHorario = date_create($sol['horario']);
                if($dtHorario){
                  $horarioTexto = $dtHorario->format('d/m/Y H:i');
                } else {
                  $horarioTexto = htmlspecialchars($sol['horario']);
                }
              } elseif(!empty($sol['criado_em'])) {
                $dtCriado = date_create($sol['criado_em']);
                if($dtCriado){
                  $horarioTexto = $dtCriado->format('d/m/Y H:i');
                }
              }
              $atualizadoTexto = '--';
              if(!empty($sol['atualizado_em'])){
                $dtAtual = date_create($sol['atualizado_em']);
                if($dtAtual){
                  $atualizadoTexto = $dtAtual->format('d/m/Y H:i');
                } else {
                  $atualizadoTexto = htmlspecialchars($sol['atualizado_em']);
                }
              }
              $tipoTexto = '--';
              if(!empty($sol['tipo'])){
                $tipoTexto = ucwords(str_replace('_',' ', $sol['tipo']));
              }
            ?>
            <tr>
              <td>
                <div class="texto-destaque"><?= htmlspecialchars($sol['estacao'] ?? '--') ?></div>
                <?php if(!empty($sol['descricao'])): ?>
                  <div class="texto-secundario"><?= nl2br(htmlspecialchars($sol['descricao'])) ?></div>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($tipoTexto) ?></td>
              <td><?= $horarioTexto ?></td>
              <td><span class="tag-prioridade prioridade-<?= htmlspecialchars($prioridadeCodigo) ?>"><?= $prioridadeTexto ?></span></td>
              <td><span class="tag-status status-<?= htmlspecialchars($statusCodigo) ?>"><?= $statusTexto ?></span></td>
              <td><?= htmlspecialchars($sol['usuario_nome'] ?? '--') ?></td>
              <td><?= $atualizadoTexto ?></td>
              <?php if(isAdmin()): ?>
              <td>
                <form method="POST" class="form-status-solicitacao">
                  <input type="hidden" name="__acao" value="atualizar_solicitacao" />
                  <input type="hidden" name="solicitacao_id" value="<?= (int)$sol['id'] ?>" />
                  <select name="nova_situacao">
                    <?php foreach($statusLabels as $codigo=>$label): ?>
                      <option value="<?= htmlspecialchars($codigo) ?>" <?= $codigo===$statusCodigo ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                  </select>
                  <label class="opcao-publicar-aviso">
                    <input type="checkbox" name="publicar_aviso" value="1" />
                    <span>Publicar aviso para os funcionários</span>
                  </label>
                  <button type="submit">Atualizar</button>
                </form>
              </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="<?= isAdmin()?8:7 ?>" style="text-align:center;color:#95a5a6;padding:20px;">Nenhuma solicitação encontrada.</td></tr>
        <?php endif; ?>
      </tbody>
      </table>
    </div>
  </section>

  <section class="form-section" id="usuarios-listagem">
    <h2>Usuários Cadastrados</h2>
    <?php if(isAdmin()): ?>
      <form method="POST" class="form-users-add">
        <input type="hidden" name="__acao" value="criar_usuario" />
        <input name="novo_nome" type="text" placeholder="Nome" required />
        <input name="novo_email" type="email" placeholder="E-mail" required />
        <input name="novo_senha" type="password" placeholder="Senha" required />
        <select name="novo_tipo">
          <option value="normal">normal</option>
          <option value="admin">admin</option>
        </select>
        <button type="submit" class="btn-user-add">Adicionar</button>
      </form>
      <?php if($msgUserAdd){ echo $msgUserAdd; } ?>
      <?php if($msgUserEdit){ echo $msgUserEdit; } ?>
      <div id="formEdicaoWrapper">
        <h3 class="form-edicao-title">Editar Usuário <span id="editUserLabel" class="form-edicao-sub"></span></h3>
        <form method="POST" class="form-edicao-usuario" id="formEditar">
          <input type="hidden" name="__acao" value="atualizar_usuario" />
          <input type="hidden" name="edit_id" id="edit_id" />
          <input name="edit_nome" id="edit_nome" type="text" placeholder="Nome" required />
          <input name="edit_email" id="edit_email" type="email" placeholder="E-mail" required />
          <select name="edit_tipo" id="edit_tipo">
            <option value="normal">normal</option>
            <option value="admin">admin</option>
          </select>
          <input name="edit_senha" id="edit_senha" type="password" placeholder="Nova senha (opcional)" />
          <button type="submit">Salvar Alterações</button>
          <button type="button" id="btnCancelarEdicao" class="btn-cancelar">Cancelar</button>
        </form>
      </div>
    <?php endif; ?>
    <div class="table-wrap">
      <table class="table-section">
        <thead>
          <tr><th>Nome</th><th>E-mail</th><th>Tipo</th><?php if(isAdmin()): ?><th>Ações</th><?php endif; ?></tr>
        </thead>
        <tbody>
          <?php if(!$usuarios): ?>
            <tr><td colspan="<?= isAdmin()?4:3 ?>">Nenhum usuário encontrado.</td></tr>
          <?php else: foreach($usuarios as $u): ?>
            <tr>
              <td><?= htmlspecialchars($u['nome']) ?><?= $u['id']===$_SESSION['usuario_id'] ? ' <span class="eu-tag">(eu)</span>' : '' ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><?= htmlspecialchars($u['tipo']) ?></td>
              <?php if(isAdmin()): ?>
              <td class="acoes-usuario">
                <a class="edit-link" href="#" data-id="<?= (int)$u['id'] ?>" data-nome="<?= htmlspecialchars($u['nome'],ENT_QUOTES) ?>" data-email="<?= htmlspecialchars($u['email'],ENT_QUOTES) ?>" data-tipo="<?= htmlspecialchars($u['tipo'],ENT_QUOTES) ?>">Editar</a>
                <?php if($u['id'] !== $_SESSION['usuario_id']): ?>
                  <form method="POST" onsubmit="return confirm('Deseja realmente excluir este usuário?');">
                    <input type="hidden" name="__acao" value="excluir_usuario" />
                    <input type="hidden" name="del_id" value="<?= (int)$u['id'] ?>" />
                    <button type="submit">Excluir</button>
                  </form>
                <?php endif; ?>
              </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <?php $conn->close(); ?>

  <script>
    (function() {
      const botaoMenu = document.querySelector('.menu-btn');
      const menuLateral = document.getElementById('menuLateral');
      const sobreposicao = document.getElementById('sobreposicaoMenu');

      function abrirMenu() {
        menuLateral.classList.add('ativo');
        sobreposicao.classList.add('ativo');
        menuLateral.setAttribute('aria-hidden', 'false');
      }

      function fecharMenu() {
        menuLateral.classList.remove('ativo');
        sobreposicao.classList.remove('ativo');
        menuLateral.setAttribute('aria-hidden', 'true');
      }

      botaoMenu.addEventListener('click', function() {
        if (menuLateral.classList.contains('ativo')) {
          fecharMenu();
        } else {
          abrirMenu();
        }
      });

      sobreposicao.addEventListener('click', function() {
        fecharMenu();
      });

      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          fecharMenu();
        }
      });
    })();
  </script>
  <?php if(isset($_SESSION['tipo']) && $_SESSION['tipo']==='admin'): ?>
  <script>
    (function(){
      const links = document.querySelectorAll('.edit-link');
      const wrap = document.getElementById('formEdicaoWrapper');
      const idF = document.getElementById('edit_id');
      const nomeF = document.getElementById('edit_nome');
      const emailF = document.getElementById('edit_email');
      const tipoF = document.getElementById('edit_tipo');
      const label = document.getElementById('editUserLabel');
      const cancelar = document.getElementById('btnCancelarEdicao');
      links.forEach(l=>{
        l.addEventListener('click', e=>{
          e.preventDefault();
          idF.value = l.dataset.id;
          nomeF.value = l.dataset.nome;
          emailF.value = l.dataset.email;
            Array.from(tipoF.options).forEach(o=>{ o.selected = (o.value===l.dataset.tipo); });
          label.textContent = '(ID '+l.dataset.id+')';
          wrap.classList.add('mostrar');
          wrap.scrollIntoView({behavior:'smooth', block:'center'});
        });
      });
      cancelar && cancelar.addEventListener('click', ()=>{
        wrap.classList.remove('mostrar');
        idF.value='';nomeF.value='';emailF.value='';tipoF.value='normal';
      });
    })();
  </script>
  <?php endif; ?>

</body>
</html>