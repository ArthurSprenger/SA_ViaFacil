<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/mqtt_notificacoes.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$conn = db_connect();
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publicar_aviso'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $mensagem_aviso = trim($_POST['mensagem'] ?? '');
    
    if ($titulo && $mensagem_aviso) {
        $stmt = $conn->prepare("INSERT INTO avisos (titulo, mensagem, usuario_id) VALUES (?, ?, ?)");
        $stmt->bind_param('ssi', $titulo, $mensagem_aviso, $_SESSION['usuario_id']);
        
        if ($stmt->execute()) {
            publicarNotificacao('aviso', $titulo, $mensagem_aviso, $_SESSION['usuario_id']);
            $mensagem = '<div class="msg-sucesso">Aviso publicado com sucesso!</div>';
        } else {
            $mensagem = '<div class="msg-erro">Erro ao publicar aviso.</div>';
        }
        $stmt->close();
    }
}

$avisos = [];
$result = $conn->query("SELECT a.*, u.nome as autor FROM avisos a INNER JOIN usuarios u ON a.usuario_id = u.id ORDER BY a.criado_em DESC LIMIT 20");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $avisos[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Avisos | Admin</title>
    <link rel="stylesheet" href="../styles/dashboard.css">
    <style>
        .conteiner-avisos {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .formulario-aviso {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .formulario-aviso h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .formulario-aviso input,
        .formulario-aviso textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        .formulario-aviso textarea {
            min-height: 100px;
            resize: vertical;
        }
        .botao-publicar {
            background: #3498db;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
        }
        .botao-publicar:hover {
            background: #2980b9;
        }
        .lista-avisos {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .item-aviso {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        .item-aviso:last-child {
            border-bottom: none;
        }
        .titulo-aviso {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .mensagem-aviso {
            color: #7f8c8d;
            margin-bottom: 8px;
        }
        .info-aviso {
            font-size: 12px;
            color: #95a5a6;
        }
        .msg-sucesso {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .msg-erro {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
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
                <li class="item-menu"><a href="publicar_avisos.php"><img src="../assets/aviso.png" class="icone-item" alt="Avisos"/><span class="texto-item">AVISOS</span></a></li>
                <li class="item-menu"><a href="conta.php"><img src="../assets/logo usuario menu.png" class="icone-item" alt="Conta"/><span class="texto-item">CONTA</span></a></li>
                <li class="item-menu"><a href="logout.php"><img src="../assets/sair.png" class="icone-item" alt="Sair"/><span class="texto-item">SAIR</span></a></li>
            </ul>
        </nav>

        <div class="sobreposicao-menu" id="sobreposicaoMenu"></div>

        <div class="conteiner-avisos">
            <div class="formulario-aviso">
                <h2>Publicar Novo Aviso</h2>
                <?= $mensagem ?>
                <form method="POST">
                    <input type="text" name="titulo" placeholder="Título do aviso" required maxlength="255">
                    <textarea name="mensagem" placeholder="Mensagem detalhada..." required></textarea>
                    <button type="submit" name="publicar_aviso" class="botao-publicar">Publicar Aviso</button>
                </form>
            </div>

            <div class="lista-avisos">
                <h2>Avisos Publicados</h2>
                <?php if (count($avisos) > 0): ?>
                    <?php foreach($avisos as $aviso): ?>
                        <div class="item-aviso">
                            <div class="titulo-aviso"><?= htmlspecialchars($aviso['titulo']) ?></div>
                            <div class="mensagem-aviso"><?= htmlspecialchars($aviso['mensagem']) ?></div>
                            <div class="info-aviso">
                                Por: <?= htmlspecialchars($aviso['autor']) ?> | 
                                <?= date('d/m/Y H:i', strtotime($aviso['criado_em'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #95a5a6; text-align: center; padding: 40px 0;">Nenhum aviso publicado ainda.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const menuBtn = document.querySelector('.menu-btn');
        const menuLateral = document.getElementById('menuLateral');
        const sobreposicao = document.getElementById('sobreposicaoMenu');

        menuBtn.addEventListener('click', () => {
            menuLateral.classList.toggle('aberto');
            sobreposicao.classList.toggle('ativo');
        });

        sobreposicao.addEventListener('click', () => {
            menuLateral.classList.remove('aberto');
            sobreposicao.classList.remove('ativo');
        });
    </script>
</body>
</html>
