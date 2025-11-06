<?php

session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$mensagem = '';

// Aprovar ou rejeitar usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'] ?? 0;
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'aprovar') {
        $stmt = $pdo->prepare("UPDATE usuarios SET status = 'aprovado' WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $mensagem = '<div class="msg-sucesso">Usuário aprovado com sucesso!</div>';
    } else if ($acao === 'rejeitar') {
        $stmt = $pdo->prepare("UPDATE usuarios SET status = 'rejeitado' WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $mensagem = '<div class="msg-erro">Usuário rejeitado.</div>';
    }
}

// Buscar usuários pendentes
$stmt = $pdo->query("SELECT id, nome, email, cep, logradouro, numero, bairro, cidade, uf, criado_em FROM usuarios WHERE status = 'pendente' ORDER BY criado_em DESC");
$usuariosPendentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar total de pendentes
$totalPendentes = count($usuariosPendentes);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Aprovar Usuários | Viafácil</title>
  <link rel="stylesheet" href="../styles/style.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 20px;
    }
    .container {
      max-width: 1200px;
      margin: 0 auto;
      background: #fff;
      padding: 24px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    h1 {
      color: #003366;
      margin-bottom: 8px;
    }
    .badge-pendentes {
      display: inline-block;
      background: #ff9800;
      color: #fff;
      padding: 4px 12px;
      border-radius: 12px;
      font-size: 14px;
      font-weight: bold;
      margin-left: 8px;
    }
    .msg-sucesso {
      background: #e8f5e9;
      color: #1b5e20;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 16px;
    }
    .msg-erro {
      background: #ffebee;
      color: #c62828;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 16px;
    }
    .usuario-card {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 16px;
      margin-bottom: 16px;
      background: #fafafa;
    }
    .usuario-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }
    .usuario-nome {
      font-size: 18px;
      font-weight: bold;
      color: #003366;
    }
    .usuario-data {
      font-size: 12px;
      color: #666;
    }
    .usuario-info {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 12px;
      margin-bottom: 16px;
    }
    .info-item {
      font-size: 14px;
      color: #333;
    }
    .info-label {
      font-weight: bold;
      color: #666;
    }
    .acoes {
      display: flex;
      gap: 12px;
    }
    .btn {
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      font-size: 14px;
    }
    .btn-aprovar {
      background: #4caf50;
      color: white;
    }
    .btn-aprovar:hover {
      background: #45a049;
    }
    .btn-rejeitar {
      background: #f44336;
      color: white;
    }
    .btn-rejeitar:hover {
      background: #da190b;
    }
    .btn-voltar {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background: #003366;
      color: white;
      text-decoration: none;
      border-radius: 6px;
    }
    .vazio {
      text-align: center;
      padding: 40px;
      color: #999;
    }
  </style>
</head>
<body>
  <div class="conteiner">
    <h1>Aprovar Usuários 
      <?php if($totalPendentes > 0): ?>
        <span class="badge-pendentes"><?= $totalPendentes ?></span>
      <?php endif; ?>
    </h1>
    
    <?= $mensagem ?>
    
    <?php if(empty($usuariosPendentes)): ?>
      <div class="vazio">
        <p>✓ Não há usuários aguardando aprovação</p>
      </div>
    <?php else: ?>
      <?php foreach($usuariosPendentes as $usuario): ?>
        <div class="cartao-usuario">
          <div class="cabecalho-usuario">
            <div class="nome-usuario"><?= htmlspecialchars($usuario['nome']) ?></div>
            <div class="data-usuario">Cadastrado em: <?= date('d/m/Y H:i', strtotime($usuario['criado_em'])) ?></div>
          </div>
          
          <div class="info-usuario">
            <div class="item-info">
              <span class="rotulo-info">Email:</span> <?= htmlspecialchars($usuario['email']) ?>
            </div>
            <div class="item-info">
              <span class="rotulo-info">CEP:</span> <?= htmlspecialchars($usuario['cep'] ?? 'Não informado') ?>
            </div>
            <?php if($usuario['logradouro']): ?>
              <div class="item-info">
                <span class="rotulo-info">Endereço:</span> 
                <?= htmlspecialchars($usuario['logradouro']) ?>, 
                <?= htmlspecialchars($usuario['numero'] ?? 'S/N') ?>
              </div>
              <div class="item-info">
                <span class="rotulo-info">Bairro:</span> <?= htmlspecialchars($usuario['bairro']) ?>
              </div>
              <div class="item-info">
                <span class="rotulo-info">Cidade/UF:</span> 
                <?= htmlspecialchars($usuario['cidade']) ?>/<?= htmlspecialchars($usuario['uf']) ?>
              </div>
            <?php endif; ?>
          </div>
          
          <form method="POST" class="acoes" style="display: inline;">
            <input type="hidden" name="usuario_id" value="<?= $usuario['id'] ?>">
            <button type="submit" name="acao" value="aprovar" class="botao botao-aprovar">✓ Aprovar</button>
            <button type="submit" name="acao" value="rejeitar" class="botao botao-rejeitar" onclick="return confirm('Tem certeza que deseja rejeitar este usuário?')">✗ Rejeitar</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
    
    <a href="dashboard.php" class="botao-voltar">← Voltar ao Dashboard</a>
  </div>
</body>
</html>
