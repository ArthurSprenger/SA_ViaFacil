
<?php
session_start();
require_once __DIR__.'/../config/db.php';
$conn = db_connect();

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

// Utilitários de segurança
function isAdmin(){ return isset($_SESSION['tipo']) && $_SESSION['tipo']==='admin'; }
function flash($key,$html){ $_SESSION[$key]=$html; }

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
        $stmtIns = $conn->prepare('INSERT INTO usuarios (nome,email,senha,tipo) VALUES (?,?,MD5(?),?)');
        $stmtIns->bind_param('ssss',$nomeNovo,$emailNovo,$senhaNova,$tipoNovo);
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
          $stmtUp = $conn->prepare('UPDATE usuarios SET nome=?, email=?, tipo=?, senha=MD5(?) WHERE id=?');
          $stmtUp->bind_param('ssssi',$nome,$email,$tipo,$senhaNova,$idEdit);
        } else {
          $stmtUp = $conn->prepare('UPDATE usuarios SET nome=?, email=?, tipo=? WHERE id=?');
          $stmtUp->bind_param('sssi',$nome,$email,$tipo,$idEdit);
        }
        if($stmtUp->execute()){
          if($idEdit===$_SESSION['usuario_id']) {
            $tipoAnterior = $_SESSION['tipo'];
            $_SESSION['tipo']=$tipo; // atualizar sessão se alterou próprio tipo
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
      $stmtIns = $conn->prepare('INSERT INTO usuarios (nome,email,senha,tipo) VALUES (?,?,MD5(?),?)');
      $stmtIns->bind_param('ssss',$nomeNovo,$emailNovo,$senhaNova,$tipoNovo);
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
      <div class="user-chip">
        <span class="user-chip__name"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
        <img class="user-chip__avatar" src="../uploads/<?= htmlspecialchars($foto) ?>" alt="Foto" />
      </div>
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
        <a href="aviso.php">
          <img src="../assets/aviso.png" alt="Ícone Aviso" />
          <span>aviso</span>
        </a>
      </article>
      <article class="card" id="solicitacao">
        <a href="solicitacoes.php">
          <img src="../assets/solicitacao.png" alt="Ícone Solicitação" />
          <span>solicitação</span>
        </a>
      </article>
    </section>
  </div>
  <section class="form-section">
    <h2>Enviar avisos</h2>
    <form id="aviso-form">
      <input type="text" id="aviso-input" placeholder="enviar avisos" required />
      <button type="submit" id="aviso-btn">ENVIAR EM AVISOS</button>
    </form>
  </section>
  <section class="form-section">
    <h2>Solicitações</h2>
    <div class="table-wrap">
      <table class="table-section">
      <thead>
        <tr>
          <th>Estação</th>
          <th>Horário</th>
          <th>Situação</th>
          <th>Ação</th>
        </tr>
      </thead>
      <tbody id="tabela-solicitacoes">
        <tr>
          <td>Central</td>
          <td>08:00</td>
          <td>Pendente</td>
          <td><button class="btn-aviso">ENVIAR SOLICITAÇÃO</button></td>
        </tr>
        <tr>
          <td>Jardim</td>
          <td>09:30</td>
          <td>Resolvido</td>
          <td><button class="btn-aviso">ENVIAR SOLICITAÇÃO</button></td>
        </tr>
        <tr>
          <td>Vila Nova</td>
          <td>10:15</td>
          <td>Pendente</td>
          <td><button class="btn-aviso">ENVIAR SOLICITAÇÃO</button></td>
        </tr> 
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
      <p class="note-md5">(Senhas armazenadas com MD5 temporariamente. Recomendado migrar para password_hash.)</p>
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

  <section class="form-section" id="sensores-placeholder">
    <h2>Monitoramento de Sensores</h2>
    <?php if(empty($sensores)): ?>
      <p class="sensors-note">Integração de sensores em desenvolvimento.</p>
      <p class="sensors-subnote">As tabelas sensor e sensor_data já estão preparadas e populadas no script SQL.</p>
    <?php else: ?>
      <div class="table-wrap">
        <table class="table-section">
          <thead>
            <tr><th>ID</th><th>Tipo</th><th>Status</th><th>Última leitura</th><th>Total Leituras</th></tr>
          </thead>
          <tbody>
          <?php foreach($sensores as $s): ?>
            <tr>
              <td><?= (int)$s['id'] ?></td>
              <td><?= htmlspecialchars($s['tipo']) ?></td>
              <td><?= htmlspecialchars($s['status']) ?></td>
              <td><?= htmlspecialchars($s['ultima_leitura']) ?></td>
              <td><?= (int)$s['total_leituras'] ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <p class="test-data-note">* Exibindo dados de teste. Visualização em tempo real será implementada.</p>
    <?php endif; ?>
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