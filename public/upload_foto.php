<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../src/User.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userRepo = new User($pdo);
$currentUser = $userRepo->getUserById($_SESSION['user_id']);
$dashboardUrl = getDashboardUrl();
$erro='';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_perfil'])){
    $targetDir = __DIR__ . '/../uploads/';
    if(!is_dir($targetDir)) @mkdir($targetDir, 0777, true);
    $basename = basename($_FILES['foto_perfil']['name']);
    $ext = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png'];
    if(!in_array($ext, $allowed)){
        $erro = 'Apenas JPG, JPEG e PNG são aceitos.';
    } else if($_FILES['foto_perfil']['size'] > 1_500_000) {
        $erro = 'Imagem muito grande (máx 1.5MB).';
    } else if(@getimagesize($_FILES['foto_perfil']['tmp_name']) === false){
        $erro = 'Arquivo enviado não é uma imagem válida.';
    } else {
        $newName = 'pfp_'.$_SESSION['user_id'].'_'.time().'.'.$ext;
        $destPath = $targetDir . $newName;
        if(move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $destPath)){
            $userRepo->updateProfilePic($_SESSION['user_id'], $newName);
            header('Location: dashboard.php');
            exit();
        } else {
            $erro = 'Falha ao mover o arquivo enviado.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Upload de Foto</title>
  <link rel="stylesheet" href="../styles/login.css" />
</head>
<body>
  <div class="page-center">
    <div class="login-card upload-foto-card">
      <img src="../assets/logo.PNG" alt="Viafacil" class="login-logo">
      <h2 class="login-title">Trocar Foto de Perfil</h2>
      <?php if($erro){ echo '<div class="erro-login">'.htmlspecialchars($erro).'</div>'; } ?>
      <form class="form-login" method="POST" action="" enctype="multipart/form-data">
        <input class="input-pill" type="file" name="foto_perfil" accept="image/*" required>
        <button class="btn-entrar" type="submit">Fazer upload</button>
        <a class="link-esqueceu" href="<?php echo htmlspecialchars($dashboardUrl); ?>">Voltar ao Dashboard</a>
      </form>
    </div>
  </div>
</body>
</html>
