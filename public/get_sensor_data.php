<?php
header('Content-Type: application/json');
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../includes/phpMQTT.php';
require_once __DIR__.'/../config/mqtt_config.php';

$client_id = MQTT_CLIENT_ID_PREFIX . "data_" . rand();
$conn = db_connect();

// Array para armazenar dados recebidos via MQTT
$dados_mqtt = [];

// Buscar sensores ativos no banco
$sensores = [];
$resSensores = $conn->query("SELECT id, tipo FROM sensor WHERE status = 'ativo' ORDER BY tipo ASC");
if ($resSensores) {
  while($r = $resSensores->fetch_assoc()) {
    $sensores[$r['tipo']] = $r['id'];
  }
}

// Coletar dados via MQTT
$mqtt_conectado = false;
$mensagens_recebidas = 0;

if (!empty($sensores)) {
    try {
        $mqtt = new Bluerhinos\phpMQTT(MQTT_SERVER, MQTT_PORT, $client_id);
        $mqtt->cafile = MQTT_CAFILE;
        $mqtt->debug = false;
        
        if ($mqtt->connect(true, NULL, MQTT_USERNAME, MQTT_PASSWORD)) {
            $mqtt_conectado = true;
            $topics_config = [];
            
            // Configurar subscrição para todos os tópicos definidos
            foreach (MQTT_TOPICS as $topico => $config) {
                $topics_config[$topico] = [
                    "qos" => 0,
                    "function" => function ($topic, $msg) use ($config, $conn, &$dados_mqtt, &$mensagens_recebidas) {
                        if (!empty($msg)) {
                            $msg = trim($msg);
                            $valor = null;
                            $unidade = $config['unidade'];
                            $tipo_sensor = $config['tipo'];
                            
                            // Processar mensagem baseado no tipo (sempre tenta converter para número)
                            if (is_numeric($msg)) {
                                $valor = floatval($msg);
                            } else {
                                // Conversões especiais para mensagens de texto
                                switch($msg) {
                                    case 'acender':
                                        $valor = 1;
                                        break;
                                    case 'apagar':
                                        $valor = 0;
                                        break;
                                    case 'objeto_proximo':
                                        $valor = 5;
                                        break;
                                    case 'objeto_longe':
                                        $valor = 50;
                                        break;
                                    default:
                                        // Tenta extrair número de string (ex: "25.5cm")
                                        if (preg_match('/^([\d.,-]+)/', $msg, $matches)) {
                                            $valor = floatval(str_replace(',', '.', $matches[1]));
                                        }
                                }
                            }
                            
                            // Se conseguiu extrair um valor válido, salvar no banco
                            if ($valor !== null) {
                                $stmt = $conn->prepare("SELECT id FROM sensor WHERE tipo = ? LIMIT 1");
                                $stmt->bind_param("s", $tipo_sensor);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                
                                if ($row = $result->fetch_assoc()) {
                                    $sensor_id = $row['id'];
                                    
                                    $stmt2 = $conn->prepare("INSERT INTO sensor_data (id_sensor, valor, unidade, data_hora) VALUES (?, ?, ?, NOW())");
                                    $stmt2->bind_param('ids', $sensor_id, $valor, $unidade);
                                    $stmt2->execute();
                                    $stmt2->close();
                                    
                                    $dados_mqtt[$sensor_id] = [
                                        'id_sensor' => $sensor_id,
                                        'valor' => $valor,
                                        'unidade' => $unidade,
                                        'tipo' => $tipo_sensor
                                    ];
                                    
                                    $mensagens_recebidas++;
                                }
                                $stmt->close();
                            }
                        }
                    }
                ];
            }
            
            // Subscribing e coletando mensagens
            if (!empty($topics_config)) {
                $mqtt->subscribe($topics_config, 0);
                
                $start = time();
                $timeout = 8;
                
                while (time() - $start < $timeout) {
                    $mqtt->proc();
                    usleep(50000);
                    
                    // Se já recebeu dados de todos os sensores ativos, pode parar antes
                    if ($mensagens_recebidas >= count($sensores)) {
                        break;
                    }
                }
            }
            
            $mqtt->close();
        }
    } catch (Exception $e) {
        // Se falhar, continua e retorna dados do banco
        error_log("Erro MQTT: " . $e->getMessage());
    }
}

// Buscar dados mais recentes do banco
$sql = "
  SELECT 
    sd.id_sensor,
    sd.valor,
    sd.unidade,
    DATE_FORMAT(sd.data_hora, '%d/%m/%Y %H:%i:%s') as data_hora,
    s.tipo
  FROM sensor_data sd
  INNER JOIN sensor s ON sd.id_sensor = s.id
  WHERE sd.id IN (
    SELECT MAX(id) 
    FROM sensor_data 
    GROUP BY id_sensor
  )
  ORDER BY s.tipo ASC
";

$result = $conn->query($sql);

$dados = [];
if ($result) {
  while($row = $result->fetch_assoc()) {
    $dados[] = $row;
  }
}

// Retornar resposta
$response = [
    'mqtt_conectado' => $mqtt_conectado,
    'mensagens_recebidas' => $mensagens_recebidas,
    'total_sensores' => count($dados),
    'dados' => $dados,
    'timestamp' => date('Y-m-d H:i:s')
];

echo json_encode($response);
$conn->close();
