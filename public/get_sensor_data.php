<?php
header('Content-Type: application/json');
require_once __DIR__.'/../config/db.php';
require(__DIR__ . '/../includes/phpMQTT.php');

// Configurações do broker MQTT
$server = "ef339175de264ab783f4bea1e2a1abe9.s1.eu.hivemq.cloud";
$port = 8883;
$client_id = "phpmqtt-data-" . rand();
$username = "Henry";
$password = "HenryDSM2";
$cafile = __DIR__ . "/../config/certs/cacert.pem";

$conn = db_connect();

// Buscar sensores ativos
$sensores_sql = "SELECT id, tipo FROM sensor WHERE status = 'ativo'";
$sensores_result = $conn->query($sensores_sql);

$sensores = [];
if ($sensores_result) {
    while ($row = $sensores_result->fetch_assoc()) {
        $sensores[] = $row;
    }
}

// Coletar dados via MQTT
if (!empty($sensores)) {
    try {
        $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);
        $mqtt->cafile = $cafile;
        
        if (!$mqtt->connect(true, NULL, $username, $password)) {
            // Falha na conexão, continua para buscar dados do banco
        } else {
            $topics_config = [];
            
            foreach ($sensores as $sensor) {
                // Formato do tópico baseado no tipo do sensor
                $topic = "S" . $sensor['id'] . "_" . $sensor['tipo'];
                
                $topics_config[$topic] = [
                    "qos" => 0,
                    "function" => function ($topic, $msg) use ($sensor, $conn) {
                        if (!empty($msg)) {
                            $msg = trim($msg);
                            
                            // Extrair valor e unidade
                            if (preg_match('/^([\d.,-]+)\s*(.*)$/', $msg, $matches)) {
                                $valor = floatval(str_replace(',', '.', $matches[1]));
                                $unidade = trim($matches[2]);
                                
                                // Se não houver unidade, definir baseado no tipo
                                if (empty($unidade)) {
                                    switch (strtolower($sensor['tipo'])) {
                                        case 'temperatura':
                                            $unidade = '°C';
                                            break;
                                        case 'umidade':
                                            $unidade = '%';
                                            break;
                                        case 'velocidade':
                                            $unidade = 'km/h';
                                            break;
                                        case 'pressao':
                                            $unidade = 'hPa';
                                            break;
                                        default:
                                            $unidade = 'un';
                                    }
                                }
                                
                                // Inserir no banco
                                $stmt = $conn->prepare("INSERT INTO sensor_data (id_sensor, valor, unidade, data_hora) VALUES (?, ?, ?, NOW())");
                                $stmt->bind_param('ids', $sensor['id'], $valor, $unidade);
                                $stmt->execute();
                                $stmt->close();
                            }
                        }
                    }
                ];
            }
            
            // Subscribing e coletando mensagens por 1-2 segundos
            $mqtt->subscribe($topics_config, 0);
            
            $start = time();
            while (time() - $start < 2) { // escuta 2 segundos
                $mqtt->proc();
            }
            
            $mqtt->close();
        }
    } catch (Exception $e) {
        // Se falhar, continua e retorna dados do banco
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

echo json_encode($dados);
$conn->close();
