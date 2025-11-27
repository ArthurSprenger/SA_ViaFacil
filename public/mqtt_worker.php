<?php
/**
 * Worker MQTT Persistente
 * Mantém conexão contínua com o broker e atualiza banco de dados
 * Execute via terminal: php public/mqtt_worker.php
 */

require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../includes/phpMQTT.php';
require_once __DIR__.'/../config/mqtt_config.php';

echo "=== Worker MQTT ViaFácil ===\n";
echo "Pressione CTRL+C para encerrar\n\n";

$client_id = MQTT_CLIENT_ID_PREFIX . "worker_" . rand();
$reconectar = true;
$tentativas = 0;
$max_tentativas = 5;

while ($reconectar) {
    try {
        $conn = db_connect();
        echo "[" . date('Y-m-d H:i:s') . "] Conectando ao broker MQTT...\n";
        
        $mqtt = new Bluerhinos\phpMQTT(MQTT_SERVER, MQTT_PORT, $client_id);
        $mqtt->cafile = MQTT_CAFILE;
        $mqtt->debug = false;
        
        if (!$mqtt->connect(true, NULL, MQTT_USERNAME, MQTT_PASSWORD)) {
            throw new Exception("Falha ao conectar ao broker");
        }
        
        echo "[" . date('Y-m-d H:i:s') . "] ✓ Conectado ao broker!\n";
        echo "[" . date('Y-m-d H:i:s') . "] Inscrevendo em " . count(MQTT_TOPICS) . " tópicos...\n\n";
        
        $tentativas = 0; // Reset tentativas após conexão bem-sucedida
        $total_mensagens = 0;
        
        // Configurar callbacks para cada tópico
        $topics_config = [];
        foreach (MQTT_TOPICS as $topico => $config) {
            $topics_config[$topico] = [
                "qos" => 0,
                "function" => function ($topic, $msg) use ($config, $conn, &$total_mensagens) {
                    $total_mensagens++;
                    $timestamp = date('Y-m-d H:i:s');
                    
                    if (!empty($msg)) {
                        $msg = trim($msg);
                        $valor = null;
                        $unidade = $config['unidade'];
                        
                        // Processar diferentes formatos de mensagem
                        if ($topic == 'S1 iluminacao') {
                            if ($msg == 'acender') {
                                $valor = 1;
                                $unidade = 'estado';
                            } else if ($msg == 'apagar') {
                                $valor = 0;
                                $unidade = 'estado';
                            }
                        } else if (strpos($topic, 'Distancia') !== false || strpos($topic, 'Ultrassom') !== false) {
                            if ($msg == 'objeto_proximo') {
                                $valor = 5;
                            } else if ($msg == 'objeto_longe') {
                                $valor = 50;
                            } else if (is_numeric($msg)) {
                                $valor = floatval($msg);
                            }
                        } else if ($topic == 'Projeto S3 Presenca3') {
                            $valor = intval($msg);
                        } else if ($topic == 'projeto trem velocidade') {
                            if (is_numeric($msg)) {
                                $valor = floatval($msg);
                            }
                        } else {
                            if (is_numeric($msg)) {
                                $valor = floatval($msg);
                            } else if (preg_match('/^([\d.,-]+)\s*(.*)$/', $msg, $matches)) {
                                $valor = floatval(str_replace(',', '.', $matches[1]));
                                if (!empty(trim($matches[2]))) {
                                    $unidade = trim($matches[2]);
                                }
                            }
                        }
                        
                        if ($valor !== null) {
                            $tipo_sensor = $config['tipo'];
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
                                
                                echo "[$timestamp] 📊 $topic → $valor $unidade (Sensor #$sensor_id)\n";
                            }
                            $stmt->close();
                        }
                    }
                }
            ];
        }
        
        $mqtt->subscribe($topics_config, 0);
        
        echo "[" . date('Y-m-d H:i:s') . "] ✓ Worker ativo! Aguardando mensagens...\n";
        echo str_repeat("-", 70) . "\n";
        
        // Loop principal - mantém conexão ativa
        $ultimo_ping = time();
        while (true) {
            $mqtt->proc();
            
            // Ping periódico para manter conexão
            if (time() - $ultimo_ping > 30) {
                echo "[" . date('Y-m-d H:i:s') . "] 💓 Keepalive (Total: $total_mensagens mensagens)\n";
                $ultimo_ping = time();
            }
            
            usleep(50000); // 50ms - reduz CPU
        }
        
    } catch (Exception $e) {
        $tentativas++;
        echo "[" . date('Y-m-d H:i:s') . "] ❌ Erro: " . $e->getMessage() . "\n";
        
        if ($tentativas >= $max_tentativas) {
            echo "[" . date('Y-m-d H:i:s') . "] ⚠ Máximo de tentativas atingido. Encerrando...\n";
            $reconectar = false;
        } else {
            echo "[" . date('Y-m-d H:i:s') . "] 🔄 Reconectando em 5 segundos... (Tentativa $tentativas/$max_tentativas)\n";
            sleep(5);
        }
    }
    
    if (isset($mqtt)) {
        try {
            $mqtt->close();
        } catch (Exception $e) {
            // Ignora erros ao fechar
        }
    }
    
    if (isset($conn)) {
        $conn->close();
    }
}

echo "[" . date('Y-m-d H:i:s') . "] Worker encerrado.\n";
