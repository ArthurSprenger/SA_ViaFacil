<?php
session_start();
require_once __DIR__ . '/../config/db.php';
$conn = db_connect();

if(!isset($_SESSION['usuario_id'])){
  header('Location: login.php');
  exit;
}

$userId = (int)$_SESSION['usuario_id'];
$msg = '';

// Processa atualização
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['__acao']) && $_POST['__acao']==='atualizar_perfil'){
  $nome = trim($_POST['nome'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $senha = trim($_POST['senha'] ?? '');
  $erros = [];
  if($nome==='') $erros[] = 'Nome obrigatório';
  if($email==='') $erros[] = 'E-mail obrigatório';
  elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido';
  // Checar duplicidade de e-mail
  if(!$erros){
    $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email=? AND id<>? LIMIT 1');
    $stmt->bind_param('si',$email,$userId);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows>0){ $erros[]='E-mail já em uso por outro usuário'; }
    $stmt->close();
  }
  if(!$erros){
    if($senha!==''){
      $stmtUp = $conn->prepare('UPDATE usuarios SET nome=?, email=?, senha=MD5(?) WHERE id=?');
      $stmtUp->bind_param('sssi',$nome,$email,$senha,$userId);
    } else {
      $stmtUp = $conn->prepare('UPDATE usuarios SET nome=?, email=? WHERE id=?');
      $stmtUp->bind_param('ssi',$nome,$email,$userId);
    }
    if($stmtUp->execute()){
      $msg = '<div class="msg-sucesso">Dados atualizados'.($senha!==''?' (senha alterada)':'').'.</div>';
    } else {
      $msg = '<div class="msg-erro">Falha ao atualizar.</div>';
    }
    $stmtUp->close();
  } else {
    $msg = '<div class="msg-erro">'.implode('<br>', array_map('htmlspecialchars',$erros)).'</div>';
  }
}

// Carregar dados atuais
$stmtUser = $conn->prepare('SELECT nome,email,tipo FROM usuarios WHERE id=? LIMIT 1');
$stmtUser->bind_param('i',$userId);
$stmtUser->execute();
$dados = $stmtUser->get_result()->fetch_assoc();
$stmtUser->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Conta | Viafácil</title>
  <style>
  body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      min-height: 100vh;
    }
    .conta-header {
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 14px 16px 18px;
      background: #003366;
      color: #fff;
      border-radius: 0 0 16px 16px;
      min-height: 120px;
    }
    .menu-btn {
      background: none;
      border: none;
      display: flex;
      flex-direction: column;
      gap: 4px;
      cursor: pointer;
      position: absolute;
      left: 12px;
      top: 12px;
      z-index: 10;
    }
    .menu-btn .bar {
      width: 28px;
      height: 4px;
      background: #fff;
      border-radius: 2px;
    }
    .logo {
      width: 96px;
      height: auto;
      display: block;
      margin: 6px auto 4px;
    }
    .conta-header h1 {
      font-size: 1.9em;
      margin: 0;
      font-weight: 700;
      line-height: 1;
    }
    .conta-container {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      max-width: 480px;
      width: 95%;
      margin: 18px auto;
      padding: 20px 18px;
      text-align: left;
    }
    .perfil-title {
      font-size: 1.3em;
      color: #111;
      margin-bottom: 14px;
    }
    .conta-form {
      display: flex;
      flex-direction: column;
      gap: 12px;
      align-items: stretch;
    }
    .conta-form label {
      text-align: left;
      color: #333;
      font-size: 1em;
      margin-bottom: 2px;
    }
    .conta-form input {
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 1em;
    }
    .conta-form button {
      padding: 10px 18px;
      border-radius: 20px;
      border: none;
      background: #43b649; 
      color: #fff;
      font-size: 1em;
      cursor: pointer;
      transition: background 0.15s ease;
      font-weight: 700;
      align-self: flex-start;
    }
    .conta-form button:hover {
      background: #2e8c34;
    }
    .msg-sucesso{background:#e6ffed;color:#216e39;padding:8px 10px;border-radius:6px;font-size:0.85rem;margin:0 0 12px;}
    .msg-erro{background:#ffefef;color:#8b1d1d;padding:8px 10px;border-radius:6px;font-size:0.85rem;margin:0 0 12px;}
    /* Menu lateral (português) */
    .menu-lateral { position: fixed; left: 0; top: 0; height: 100vh; width: 260px; background: #2f2f2f; color: #fff; padding-top: 28px; box-shadow: 2px 0 12px rgba(0,0,0,0.3); transform: translateX(-110%); transition: transform 0.28s ease; z-index: 1000; }
    .menu-lateral.ativo { transform: translateX(0); }
    .sobreposicao-menu { position: fixed; left: 0; top: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.4); opacity: 0; visibility: hidden; transition: opacity 0.2s ease; z-index: 900; }
    .sobreposicao-menu.ativo { opacity: 1; visibility: visible; }
    .lista-itens { list-style: none; padding: 0 12px; margin: 0; }
    .item-menu { display:flex; align-items:center; gap:12px; padding:14px 8px; border-radius:8px; color:#fff; cursor:pointer; margin-bottom:8px; }
    .item-menu:hover { background: rgba(255,255,255,0.04); }
    .item-menu a { color: inherit; text-decoration: none; display:flex; align-items:center; gap:12px; width:100%; }
    .icone-item { width:36px; height:36px; display:block; }
    .texto-item { font-weight:700; font-size:0.95em; }
    @media (max-width: 900px) {
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
      .conta-header {
        flex-direction: column;
        gap: 8px;
        padding: 10px 8px;
      }
      .logo {
        max-width: 50px;
      }
      .conta-container {
        padding: 12px 4px;
      }
      .sidebar-menu {
        width: 80px;
        padding-top: 20px;
      }
      .sidebar-menu li {
        gap: 4px;
        padding-left: 2px;
      }
      .sidebar-icon {
        max-width: 16px;
      }
    }
  </style>
</head>
<body>
  <header class="conta-header">
    <button class="menu-btn" aria-label="Abrir menu">
      <span class="bar"></span>
      <span class="bar"></span>
      <span class="bar"></span>
    </button>
    <a href="dashboard.php">
      <img src="../assets/logo.PNG" alt="Viafacil" class="logo" />
    </a>
    <h1>Conta</h1>
  </header>
  <main class="conta-container">
    <div class="perfil-title">Perfil</div>
    <?= $msg ?>
    <form class="conta-form" method="POST" action="">
      <input type="hidden" name="__acao" value="atualizar_perfil" />
      <label for="nome">Nome</label>
      <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($dados['nome'] ?? '') ?>" required />
      <label for="email">E-mail</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($dados['email'] ?? '') ?>" required />
      <label for="senha">Nova Senha (opcional)</label>
      <input type="password" id="senha" name="senha" placeholder="Deixe em branco para manter" />
      <button type="submit">Salvar alterações</button>
      <p style="font-size:0.7rem;color:#555;margin:4px 0 0;">Senhas armazenadas temporariamente com MD5 (modo protótipo).</p>
    </form>
  </main>
  <nav class="menu-lateral" id="menuLateral">
    <ul class="lista-itens">
      <li class="item-menu"><a href="dashboard.php"><img src="../assets/dashboard.png" class="icone-item" alt="Dashboard"/><span class="texto-item">DASHBOARD</span></a></li>
      <li class="item-menu"><a href="conta.php"><img src="../assets/logo usuario menu.png" class="icone-item" alt="Conta"/><span class="texto-item">CONTA</span></a></li>
      <li class="item-menu"><a href="configs.php"><img src="../assets/configurações.png" class="icone-item" alt="Configurações"/><span class="texto-item">CONFIGURAÇÕES</span></a></li>
      <li class="item-menu"><a href="login.php"><img src="../assets/sair.png" class="icone-item" alt="Sair"/><span class="texto-item">SAIR</span></a></li>
    </ul>
  </nav>
  <div class="sobreposicao-menu" id="sobreposicaoMenu"></div>

  <script>
    (function(){
      const botaoMenu = document.querySelector('.menu-btn');
      const menuLateral = document.getElementById('menuLateral');
      const sobreposicao = document.getElementById('sobreposicaoMenu');

      function abrirMenu(){
        menuLateral.classList.add('ativo');
        sobreposicao.classList.add('ativo');
        menuLateral.setAttribute('aria-hidden','false');
      }
      function fecharMenu(){
        menuLateral.classList.remove('ativo');
        sobreposicao.classList.remove('ativo');
        menuLateral.setAttribute('aria-hidden','true');
      }

      botaoMenu.addEventListener('click', function(){
        if(menuLateral.classList.contains('ativo')) fecharMenu(); else abrirMenu();
      });

      sobreposicao.addEventListener('click', fecharMenu);

      document.addEventListener('keydown', function(e){ if(e.key === 'Escape') fecharMenu(); });

      Array.from(menuLateral.querySelectorAll('a')).forEach(function(link){
        link.addEventListener('click', function(){
          fecharMenu();
        });
      });
    })();
  </script>
</body>
</html>