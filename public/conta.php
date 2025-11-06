<?php
session_start();
require_once __DIR__ . '/../config/db.php';
$conn = db_connect();
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/helpers.php';

if(!isset($_SESSION['usuario_id'])){
  header('Location: login.php');
  exit;
}

$userId = (int)$_SESSION['usuario_id'];
$dashboardUrl = getDashboardUrl();
$msg = '';

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['__acao']) && $_POST['__acao']==='atualizar_perfil'){
  $nome = trim($_POST['nome'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $senha = trim($_POST['senha'] ?? '');
  $erros = [];
  if($nome==='') $erros[] = 'Nome obrigatório';
  if($email==='') $erros[] = 'E-mail obrigatório';
  elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido';
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
  <link rel="stylesheet" href="../styles/conta.css" />
</head>
<body>
  <header class="conta-header">
    <button class="menu-btn" aria-label="Abrir menu">
      <span class="bar"></span>
      <span class="bar"></span>
      <span class="bar"></span>
    </button>
    <a href="<?php echo htmlspecialchars($dashboardUrl); ?>">
      <img src="../assets/logo.PNG" alt="Viafacil" class="logo" />
    </a>
    <h1>Conta</h1>
  </header>
  <main class="conta-container">
    <?php
      $foto = null;
      try{
        $stmt = $pdo->prepare('SELECT foto_perfil FROM usuarios WHERE id = :id');
        $stmt->bindParam(':id', $_SESSION['usuario_id']);
        $stmt->execute();
        $row = $stmt->fetch();
        $foto = $row['foto_perfil'] ?? 'default.jpg';
      } catch(Throwable $e) { $foto = 'default.jpg'; }
    ?>
    <div class="profile-photo-section">
      <img class="profile-photo" src="../uploads/<?= htmlspecialchars($foto) ?>" alt="Foto de Perfil" />
      <a href="upload_foto.php" class="link-change-photo">Trocar foto</a>
    </div>
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
      <p class="note-md5">Senhas armazenadas temporariamente com MD5 (modo protótipo).</p>
    </form>
  </main>
  <nav class="menu-lateral" id="menuLateral">
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