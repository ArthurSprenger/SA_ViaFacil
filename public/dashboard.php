
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
          if($idEdit===$_SESSION['usuario_id']) $_SESSION['tipo']=$tipo; // atualizar sessão se alterou próprio tipo
          flash('flash_user_edit','<div class="msg-sucesso">Usuário atualizado.</div>');
        } else flash('flash_user_edit','<div class="msg-erro">Erro ao atualizar.</div>');
        $stmtUp->close();
      }
      $stmt->close();
    }
    header('Location: dashboard.php?edit_user='.$idEdit.'#usuarios-listagem');exit;
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

// Obter usuário para edição
$usuarioEdicao = null;
if(isAdmin() && isset($_GET['edit_user'])){
  $idE = (int)$_GET['edit_user'];
  $stmtE = $conn->prepare('SELECT id,nome,email,tipo FROM usuarios WHERE id=?');
  $stmtE->bind_param('i',$idE);$stmtE->execute();
  $resE = $stmtE->get_result();
  if($resE && $resE->num_rows){ $usuarioEdicao = $resE->fetch_assoc(); }
  $stmtE->close();
}

// Tratamento de criação de usuário (somente admin)
if(isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin' && $_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['__acao']) && $_POST['__acao']==='criar_usuario'){
  $nomeNovo = trim($_POST['novo_nome'] ?? '');
  $emailNovo = trim($_POST['novo_email'] ?? '');
  $senhaNova = trim($_POST['novo_senha'] ?? '');
  $tipoNovo  = ($_POST['novo_tipo'] ?? 'normal') === 'admin' ? 'admin':'normal';
  if(!$nomeNovo || !$emailNovo || !$senhaNova){
    $_SESSION['flash_user_add'] = '<div style="background:#ffefef;color:#8b1d1d;padding:6px 10px;border-radius:6px;font-size:0.85rem;">Preencha todos os campos para criar o usuário.</div>';
  } else if(!filter_var($emailNovo, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_user_add'] = '<div style="background:#ffefef;color:#8b1d1d;padding:6px 10px;border-radius:6px;font-size:0.85rem;">E-mail inválido.</div>';
  } else {
    // Verificar duplicidade
    $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email=? LIMIT 1');
    $stmt->bind_param('s',$emailNovo);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows>0){
      $_SESSION['flash_user_add'] = '<div style="background:#fff3cd;color:#8a6d3b;padding:6px 10px;border-radius:6px;font-size:0.85rem;">Já existe usuário com esse e-mail.</div>';
    } else {
      $stmtIns = $conn->prepare('INSERT INTO usuarios (nome,email,senha,tipo) VALUES (?,?,MD5(?),?)');
      $stmtIns->bind_param('ssss',$nomeNovo,$emailNovo,$senhaNova,$tipoNovo);
      if($stmtIns->execute()){
        $_SESSION['flash_user_add'] = '<div style="background:#e6ffed;color:#216e39;padding:6px 10px;border-radius:6px;font-size:0.85rem;">Usuário criado com sucesso.</div>';
      } else {
        $_SESSION['flash_user_add'] = '<div style="background:#ffefef;color:#8b1d1d;padding:6px 10px;border-radius:6px;font-size:0.85rem;">Erro ao criar usuário.</div>';
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
  <style>
  body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      min-height: 100vh;
    }
    .dashboard-bg {
      width: 100%;
      background: #003366;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      border-radius: 0 0 16px 16px;
      padding-bottom: 24px;
    }
    .header {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 18px 16px;
      background: #003366;
      border-radius: 0 0 16px 16px;
      min-height: 64px;
    }
    .logo {
      width: 160px;
      height: auto;
      display: block;
    }
    .menu-btn {
      background: none;
      border: none;
      display: flex;
      flex-direction: column;
      gap: 4px;
      cursor: pointer;
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      z-index: 10;
    }
    .bar {
      width: 28px;
      height: 4px;
      background: #fff;
      border-radius: 2px;
    }
    .cards {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 24px;
      padding: 24px;
      justify-items: center;
      background: #003366;
    }
    .card {
      background: #e6e6e6;
      border-radius: 16px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.10);
      width: 100%;
      max-width: 160px;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: box-shadow 0.2s;
      padding: 18px;
    }
    .card img {
      max-width: 64px;
      margin-bottom: 12px;
    }
    .card span {
      font-size: 1.15em;
      color: #222;
      font-weight: bold;
    }
    .card a { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px; color:inherit; text-decoration:none; width:100%; height:100%; }
    .form-section {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      max-width: 600px;
      width: 95%;
      margin: 24px auto;
      padding: 24px 18px;
    }
    .form-section h2 {
      margin-top: 0;
      color: #007bff;
      font-size: 1.3em;
    }
    .form-section form {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 12px;
    }
    .form-section input {
      flex: 1 1 120px;
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 1em;
    }
    .form-section button {
      padding: 8px 16px;
      border-radius: 5px;
      border: none;
      background: #43b649;
      color: #fff;
      font-size: 1em;
      cursor: pointer;
      transition: background 0.2s;
    }
    .form-section button:hover {
      background: #2e8c34;
    }
    .table-section {
      width: 100%;
      border-collapse: collapse;
      margin-top: 12px;
    }
    .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius:6px; }
    .table-section th, .table-section td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: center;
    }
    .table-section th {
      background: #f1f1f1;
      color: #007bff;
    }
    .btn-aviso {
      background: #43b649;
      color: #fff;
      border: none;
      border-radius: 5px;
      padding: 6px 10px;
      cursor: pointer;
      font-size: 0.95em;
      transition: background 0.2s;
      font-weight: bold;
    }
    .btn-aviso:hover {
      background: #2e8c34;
    }
    .sidebar-menu {
      position: fixed;
      left: 0;
      top: 0;
      height: 100vh;
      width: 180px;
      background: #007bff;
      color: #fff;
      padding-top: 60px;
      box-shadow: 2px 0 8px rgba(0,0,0,0.08);
      z-index: 100;
    }
    .sidebar-menu ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .sidebar-menu li {
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      gap: 10px;
      padding-left: 18px;
    }
    .sidebar-menu a {
      color: #fff;
      text-decoration: none;
      font-size: 1em;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .sidebar-icon {
      max-width: 28px;
      vertical-align: middle;
    }
    @media (max-width: 900px) {
      .cards {
        grid-template-columns: 2fr 2fr;
        gap: 18px;
        padding: 18px;
      }
      .sidebar-menu {
        width: 120px;
        padding-top: 40px;
      }
      .sidebar-menu li {
        gap: 6px;
        padding-left: 8px;
      }
      .sidebar-icon {
        max-width: 20px;
      }
    }
    @media (max-width: 600px) {
      .header {
        flex-direction: column;
        gap: 8px;
        padding: 10px 8px;
      }
      .logo {
        max-width: 70px;
      }
      .cards {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        padding: 12px;
      }
      .card {
        max-width: 100%;
        padding: 18px 4px;
      }
      .form-section {
        padding: 12px 4px;
      }
      .table-section th, .table-section td { padding: 6px; font-size: 0.95em; }
      .btn-aviso { padding: 6px 8px; font-size: 0.85em; }
      .sidebar-icon {
        max-width: 16px;
      }
    }
    .menu-lateral { position: fixed; left: 0; top: 0; height: 100vh; width: 260px; background: #2f2f2f; color: #fff; padding-top: 28px; box-shadow: 2px 0 12px rgba(0,0,0,0.3); transform: translateX(-110%); transition: transform 0.28s ease; z-index: 1000; }
    .menu-lateral.ativo { transform: translateX(0); }
    .sobreposicao-menu { position: fixed; left: 0; top: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.4); opacity: 0; visibility: hidden; transition: opacity 0.2s ease; z-index: 900; }
    .sobreposicao-menu.ativo { opacity: 1; visibility: visible; }
    .lista-itens { list-style: none; padding: 0 12px; margin: 0; }
    .item-menu { display:flex; align-items:center; gap:12px; padding:14px 8px; border-radius:8px; color:#fff; cursor:pointer; margin-bottom:8px; }
    .item-menu:hover { background: rgba(255,255,255,0.04); }
    .item-menu a { color: inherit; text-decoration: none; display: flex; align-items: center; gap: 12px; width: 100%; }
    .icone-item { width:36px; height:36px; display:block; }
  .texto-item { font-weight:700; font-size:0.95em; }
  /* mensagens */
  .msg-sucesso{background:#e6ffed;color:#216e39;padding:6px 10px;border-radius:6px;font-size:0.85rem;margin:0 0 8px;}
  .msg-erro{background:#ffefef;color:#8b1d1d;padding:6px 10px;border-radius:6px;font-size:0.85rem;margin:0 0 8px;}
  .msg-alerta{background:#fff3cd;color:#8a6d3b;padding:6px 10px;border-radius:6px;font-size:0.85rem;margin:0 0 8px;}
  .acoes-usuario form{display:inline;}
  .acoes-usuario button{background:#d9534f;color:#fff;border:0;border-radius:4px;padding:4px 8px;font-size:0.7rem;cursor:pointer;}
  .acoes-usuario a.edit-link{background:#007bff;color:#fff;padding:4px 8px;border-radius:4px;font-size:0.7rem;text-decoration:none;display:inline-block;margin-right:4px;}
  .acoes-usuario a.edit-link:hover{background:#0064cc;}
  .acoes-usuario button:hover{background:#b52f2a;}
  .form-edicao-usuario input,.form-edicao-usuario select{padding:8px;border:1px solid #ccc;border-radius:5px;font-size:0.9rem;}
  .form-edicao-usuario{display:flex;flex-wrap:wrap;gap:10px;margin:0 0 12px;}
  .form-edicao-usuario button{background:#007bff;color:#fff;border:0;border-radius:6px;padding:10px 16px;font-weight:600;cursor:pointer;}
  .form-edicao-usuario button:hover{background:#0064cc;}
  </style>
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
    </header>
    <nav class="menu-lateral" id="menuLateral">
      <ul class="lista-itens">
        <li class="item-menu"><a href="dashboard.php"><img src="../assets/dashboard.png" class="icone-item" alt="Dashboard"/><span class="texto-item">DASHBOARD</span></a></li>
        <li class="item-menu"><a href="conta.php"><img src="../assets/logo usuario menu.png" class="icone-item" alt="Conta"/><span class="texto-item">CONTA</span></a></li>
        <li class="item-menu"><a href="configs.php"><img src="../assets/configurações.png" class="icone-item" alt="Configurações"/><span class="texto-item">CONFIGURAÇÕES</span></a></li>
        <li class="item-menu"><a href="login.php"><img src="../assets/sair.png" class="icone-item" alt="Sair"/><span class="texto-item">SAIR</span></a></li>
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
      <form method="POST" style="display:flex;flex-wrap:wrap;gap:8px;margin:0 0 14px;align-items:flex-end;">
        <input type="hidden" name="__acao" value="criar_usuario" />
        <input name="novo_nome" type="text" placeholder="Nome" required style="flex:1 1 140px;min-width:140px;" />
        <input name="novo_email" type="email" placeholder="E-mail" required style="flex:1 1 180px;min-width:180px;" />
        <input name="novo_senha" type="password" placeholder="Senha" required style="flex:1 1 140px;min-width:140px;" />
        <select name="novo_tipo" style="flex:0 0 130px;">
          <option value="normal">normal</option>
          <option value="admin">admin</option>
        </select>
        <button type="submit" style="background:#007bff;color:#fff;border:0;border-radius:6px;padding:8px 14px;font-weight:600;cursor:pointer;">Adicionar</button>
      </form>
      <?php if($msgUserAdd){ echo $msgUserAdd; } ?>
      <?php if($usuarioEdicao): ?>
        <h3 style="margin:8px 0 4px;font-size:1.05rem;">Editar Usuário (ID <?= (int)$usuarioEdicao['id']?>)</h3>
        <?php if($msgUserEdit){ echo $msgUserEdit; } ?>
        <form method="POST" class="form-edicao-usuario">
          <input type="hidden" name="__acao" value="atualizar_usuario" />
          <input type="hidden" name="edit_id" value="<?= (int)$usuarioEdicao['id'] ?>" />
          <input name="edit_nome" type="text" value="<?= htmlspecialchars($usuarioEdicao['nome']) ?>" placeholder="Nome" required style="flex:1 1 160px;" />
          <input name="edit_email" type="email" value="<?= htmlspecialchars($usuarioEdicao['email']) ?>" placeholder="E-mail" required style="flex:1 1 200px;" />
          <select name="edit_tipo" style="flex:0 0 140px;">
            <option value="normal" <?= $usuarioEdicao['tipo']==='normal'?'selected':''; ?>>normal</option>
            <option value="admin" <?= $usuarioEdicao['tipo']==='admin'?'selected':''; ?>>admin</option>
          </select>
          <input name="edit_senha" type="password" placeholder="Nova senha (opcional)" style="flex:1 1 180px;" />
          <button type="submit">Salvar Alterações</button>
          <a href="dashboard.php#usuarios-listagem" style="color:#555;font-size:0.8rem;text-decoration:none;align-self:center;">Cancelar</a>
        </form>
      <?php else: if($msgUserEdit){ echo $msgUserEdit; } endif; ?>
      <p style="margin:4px 0 14px;font-size:0.75rem;color:#555;">(Senhas armazenadas com MD5 temporariamente. Recomendado migrar para password_hash.)</p>
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
              <td><?= htmlspecialchars($u['nome']) ?><?= $u['id']===$_SESSION['usuario_id'] ? ' <span style="color:#007bff;font-size:0.7rem;font-weight:600;">(eu)</span>' : '' ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><?= htmlspecialchars($u['tipo']) ?></td>
              <?php if(isAdmin()): ?>
              <td class="acoes-usuario">
                <a class="edit-link" href="dashboard.php?edit_user=<?= (int)$u['id'] ?>#usuarios-listagem">Editar</a>
                <?php if($u['id'] !== $_SESSION['usuario_id']): ?>
                  <form method="POST" onsubmit="return confirm('Deseja realmente excluir este usuário?');" style="display:inline;">
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
      <p style="margin:0 0 8px;">Integração de sensores em desenvolvimento.</p>
      <p style="margin:0; font-size:0.9rem; color:#555;">As tabelas sensor e sensor_data já estão preparadas e populadas no script SQL.</p>
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
      <p style="margin:10px 0 0; font-size:0.85rem; color:#444;">* Exibindo dados de teste. Visualização em tempo real será implementada.</p>
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

</body>
</html>