<?php
require_once __DIR__.'/../includes/phpMQTT.php';
require_once __DIR__.'/../config/mqtt_config.php';

$message = "";
$client_id = MQTT_CLIENT_ID_PREFIX . rand();

try {
    $mqtt = new Bluerhinos\phpMQTT(MQTT_SERVER, MQTT_PORT, $client_id);
    $mqtt->cafile = MQTT_CAFILE;
    
    if (!$mqtt->connect(true, NULL, MQTT_USERNAME, MQTT_PASSWORD)) {
        echo json_encode(['error' => 'Não foi possível conectar ao broker MQTT']);
        exit;
    }

    // Subscribing e coletando mensagens
    $mqtt->subscribe([
        MQTT_TOPIC => [
            "qos" => 0,
            "function" => function ($topic, $msg) use (&$message) {
                if (!empty($msg)) {
                    $message = $msg;
                }
            }
        ]
    ], 0);

    $start = time();
    while (time() - $start < 2) {
        $mqtt->proc();
    }

    $mqtt->close();

    if (!empty($message)) {
        // Espera-se que a mensagem seja JSON: {"tipo":"temperatura","valor":25.5,"unidade":"°C"}
        $data = json_decode($message, true);
        if ($data) {
            // Salvar no banco de dados
            require_once __DIR__.'/../config/db.php';
            $conn = db_connect();
            
            // Buscar o sensor pelo tipo
            $stmt = $conn->prepare("SELECT id FROM sensor WHERE tipo = ? LIMIT 1");
            $stmt->bind_param("s", $data['tipo']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $sensor_id = $row['id'];
                
                // Inserir os dados do sensor
                $stmt2 = $conn->prepare("INSERT INTO sensor_data (id_sensor, valor, unidade) VALUES (?, ?, ?)");
                $stmt2->bind_param("ids", $sensor_id, $data['valor'], $data['unidade']);
                $stmt2->execute();
                $stmt2->close();
            }
            
            $stmt->close();
            $conn->close();
            
            echo json_encode($data);
        } else {
            echo $message;
        }
    } else {
        echo json_encode(['message' => 'Nenhuma mensagem recebida']);
    }

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
