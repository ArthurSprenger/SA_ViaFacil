<?php

require_once __DIR__ . '/includes/phpMQTT.php';
require_once __DIR__ . '/config/db.php';

$server = "broker.hivemq.com";
$port = 1883;
$topic = "viafacil/sensores/#";
$client_id = "viafacil_subscriber_" . uniqid();

echo "[" . date('Y-m-d H:i:s') . "] Iniciando cliente MQTT assinante...\n";
echo "[" . date('Y-m-d H:i:s') . "] Conectando ao broker: $server:$port\n";
echo "[" . date('Y-m-d H:i:s') . "] Tópico: $topic\n";
echo str_repeat("-", 60) . "\n";

$conn = db_connect();

$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

if (!$mqtt->connect(true, NULL, "", "")) {
    die("[ERRO] Não foi possível conectar ao broker MQTT\n");
}

echo "[" . date('Y-m-d H:i:s') . "] ✓ Conectado ao broker com sucesso!\n";

$mqtt->subscribe([
    $topic => [
        "qos" => 0, 
        "function" => function ($topic, $msg) use ($conn) {
            $timestamp = date('Y-m-d H:i:s');
            echo "\n[$timestamp] Nova mensagem recebida!\n";
            echo "  Tópico: $topic\n";
            echo "  Payload: $msg\n";
            
            $data = json_decode($msg, true);
            
            if (!$data) {
                echo "  [ERRO] JSON inválido - mensagem ignorada\n";
                return;
            }
            
            $tipo = $data['tipo'] ?? null;
            $valor = $data['valor'] ?? null;
            $unidade = $data['unidade'] ?? null;
            
            if (!$tipo || $valor === null || !$unidade) {
                echo "  [ERRO] Campos obrigatórios ausentes (tipo, valor, unidade)\n";
                return;
            }
            
            $stmt = $conn->prepare("SELECT id FROM sensor WHERE tipo = ? AND status = 'ativo' LIMIT 1");
            $stmt->bind_param('s', $tipo);
            $stmt->execute();
            $result = $stmt->get_result();
            $sensor = $result->fetch_assoc();
            $stmt->close();
            
            if (!$sensor) {
                echo "  [AVISO] Sensor tipo '$tipo' não encontrado - criando novo sensor...\n";
                
                $descricao = "Sensor $tipo - Auto-criado via MQTT";
                $stmtInsert = $conn->prepare("INSERT INTO sensor (tipo, descricao, status) VALUES (?, ?, 'ativo')");
                $stmtInsert->bind_param('ss', $tipo, $descricao);
                
                if ($stmtInsert->execute()) {
                    $id_sensor = $stmtInsert->insert_id;
                    echo "  [OK] Novo sensor criado com ID: $id_sensor\n";
                } else {
                    echo "  [ERRO] Falha ao criar sensor: " . $stmtInsert->error . "\n";
                    $stmtInsert->close();
                    return;
                }
                $stmtInsert->close();
            } else {
                $id_sensor = $sensor['id'];
            }
            
            $stmtData = $conn->prepare("INSERT INTO sensor_data (id_sensor, valor, unidade, data_hora) VALUES (?, ?, ?, NOW())");
            $stmtData->bind_param('ids', $id_sensor, $valor, $unidade);
            
            if ($stmtData->execute()) {
                echo "  [✓] Dados salvos no banco com sucesso!\n";
                echo "      ID Sensor: $id_sensor | Valor: $valor $unidade\n";
            } else {
                echo "  [ERRO] Falha ao salvar dados: " . $stmtData->error . "\n";
            }
            $stmtData->close();
        }
    ]
], 0);

echo "[" . date('Y-m-d H:i:s') . "] ✓ Inscrito no tópico com sucesso!\n";
echo "[" . date('Y-m-d H:i:s') . "] Aguardando mensagens... (Ctrl+C para sair)\n";
echo str_repeat("-", 60) . "\n";

while (true) {
    $mqtt->proc();
    usleep(100000);
}

$mqtt->close();
