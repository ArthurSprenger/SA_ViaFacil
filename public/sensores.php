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
  <link rel="stylesheet" href="../styles/sensores.css">
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
    
    <h1 class="titulo-pagina">Monitoramento de Sensores IoT</h1>
    <div class="info-atualizacao">
      <span>Atualização automática a cada 3 segundos</span>
      <span id="worker-status" style="margin-left: 1rem;"></span>
    </div>
    
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
        if (!response.ok) {
          throw new Error('Erro na requisição');
        }
        
        const result = await response.json();
        
        // Atualizar indicador de conexão MQTT
        const infoDiv = document.querySelector('.info-atualizacao');
        const workerStatus = document.getElementById('worker-status');
        
        if (result.mqtt_conectado) {
          infoDiv.style.color = '#28a745';
          const msg = result.mensagens_recebidas > 0 
            ? `🟢 MQTT Online (${result.mensagens_recebidas} msgs)`
            : '🟡 MQTT Online (aguardando dados)';
          workerStatus.innerHTML = msg;
        } else {
          infoDiv.style.color = '#ffc107';
          workerStatus.innerHTML = '⚠️ Conectando...';
        }
        
        const data = result.dados || result;
        
        if (data && data.length > 0) {
          data.forEach(sensor => {
            const card = document.querySelector(`[data-sensor-id="${sensor.id_sensor}"]`);
            if (card) {
              const valorDiv = card.querySelector('.valor-sensor');
              const timestampDiv = card.querySelector('.timestamp-sensor');
              
              // Formatar o valor com até 2 casas decimais
              const valorFormatado = parseFloat(sensor.valor).toFixed(2);
              valorDiv.innerHTML = `${valorFormatado}<span class="unidade-sensor">${sensor.unidade}</span>`;
              valorDiv.classList.remove('carregando-sensor');
              
              timestampDiv.textContent = `Última leitura: ${sensor.data_hora}`;
            }
          });
        }
      } catch (error) {
        console.error('Erro ao atualizar sensores:', error);
        const infoDiv = document.querySelector('.info-atualizacao');
        infoDiv.textContent = '❌ Erro de conexão';
        infoDiv.style.color = '#dc3545';
      }
    }

    // Atualizar imediatamente ao carregar
    atualizarSensores();
    
    // Atualizar a cada 3 segundos
    setInterval(atualizarSensores, 3000);
  </script>
</body>
</html>
