<?php
session_start();
require_once __DIR__.'/../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

$conn = db_connect();

$sensores = [];
$resSensores = $conn->query("SELECT id, tipo, descricao, status FROM sensor ORDER BY tipo ASC");
if ($resSensores) { 
  while($r = $resSensores->fetch_assoc()) { 
    $sensores[] = $r; 
  } 
}

$dashboardUrl = ($_SESSION['tipo'] ?? 'normal') === 'admin' ? 'dashboard.php' : 'dashboard_funcionario.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Monitoramento de Sensores - ViaFácil</title>
  <link rel="stylesheet" href="../styles/dashboard.css">
  <style>
    .grade-sensores {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      padding: 20px;
      max-width: 1400px;
      margin: 0 auto;
    }
    
    .cartao-sensor {
      background: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: transform 0.2s;
    }
    
    .cartao-sensor:hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .cabecalho-sensor {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      padding-bottom: 15px;
      border-bottom: 2px solid #f0f0f0;
    }
    
    .tipo-sensor {
      font-size: 18px;
      font-weight: 600;
      color: #2c3e50;
      text-transform: uppercase;
    }
    
    .status-sensor {
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }
    
    .status-ativo {
      background: #d4edda;
      color: #155724;
    }
    
    .status-inativo {
      background: #f8d7da;
      color: #721c24;
    }
    
    .valor-sensor {
      font-size: 36px;
      font-weight: 700;
      color: #3498db;
      margin: 10px 0;
    }
    
    .unidade-sensor {
      font-size: 18px;
      color: #7f8c8d;
      margin-left: 5px;
    }
    
    .timestamp-sensor {
      font-size: 12px;
      color: #95a5a6;
      margin-top: 10px;
    }
    
    .carregando-sensor {
      color: #95a5a6;
      font-style: italic;
    }
    
    .titulo-pagina {
      text-align: center;
      padding: 30px 20px 10px;
      font-size: 28px;
      font-weight: 600;
      color: #2c3e50;
    }
    
    .info-atualizacao {
      text-align: center;
      color: #7f8c8d;
      font-size: 14px;
      padding-bottom: 10px;
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
      <a href="<?= $dashboardUrl ?>">
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
        <li class="item-menu"><a href="<?= $dashboardUrl ?>"><img src="../assets/dashboard.png" class="icone-item" alt="Dashboard"/><span class="texto-item">DASHBOARD</span></a></li>
        <li class="item-menu"><a href="conta.php"><img src="../assets/logo usuario menu.png" class="icone-item" alt="Conta"/><span class="texto-item">CONTA</span></a></li>
        <li class="item-menu"><a href="sensores.php"><img src="../assets/trens.png" class="icone-item" alt="Sensores"/><span class="texto-item">SENSORES</span></a></li>
        <li class="item-menu"><a href="configs.php"><img src="../assets/configurações.png" class="icone-item" alt="Configurações"/><span class="texto-item">CONFIGURAÇÕES</span></a></li>
        <li class="item-menu"><a href="logout.php"><img src="../assets/sair.png" class="icone-item" alt="Sair"/><span class="texto-item">SAIR</span></a></li>
      </ul>
    </nav>
    
    <div class="sobreposicao-menu" id="sobreposicaoMenu"></div>
    
    <h1 class="titulo-pagina">🔧 Monitoramento de Sensores IoT</h1>
    <div class="info-atualizacao">Atualização automática a cada 3 segundos</div>
    
    <div class="grade-sensores">
      <?php foreach($sensores as $sensor): ?>
        <div class="cartao-sensor" data-sensor-id="<?= $sensor['id'] ?>">
          <div class="cabecalho-sensor">
            <span class="tipo-sensor"><?= htmlspecialchars(str_replace('_', ' ', $sensor['tipo'])) ?></span>
            <span class="status-sensor status-<?= $sensor['status'] ?>"><?= strtoupper($sensor['status']) ?></span>
          </div>
          <div class="valor-sensor carregando-sensor">Aguardando dados...</div>
          <div class="timestamp-sensor"></div>
        </div>
      <?php endforeach; ?>
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

    async function atualizarSensores() {
      try {
        const response = await fetch('get_sensor_data.php');
        const data = await response.json();
        
        data.forEach(sensor => {
          const card = document.querySelector(`[data-sensor-id="${sensor.id_sensor}"]`);
          if (card) {
            const valorDiv = card.querySelector('.valor-sensor');
            const timestampDiv = card.querySelector('.timestamp-sensor');
            
            valorDiv.innerHTML = `${sensor.valor}<span class="unidade-sensor">${sensor.unidade}</span>`;
            valorDiv.classList.remove('carregando-sensor');
            
            timestampDiv.textContent = `Última leitura: ${sensor.data_hora}`;
          }
        });
      } catch (error) {
        console.error('Erro ao atualizar sensores:', error);
      }
    }

    atualizarSensores();
    setInterval(atualizarSensores, 3000);
  </script>
</body>
</html>
