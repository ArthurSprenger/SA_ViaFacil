<?php
require(__DIR__ . '/../includes/phpMQTT.php');

// Configurações do broker MQTT
$server = "ef339175de264ab783f4bea1e2a1abe9.s1.eu.hivemq.cloud";
$port = 8883;
$client_id = "phpmqtt-test-" . rand();
$username = "Henry";
$password = "HenryDSM2";
$cafile = __DIR__ . "/../config/certs/cacert.pem";

// Tópicos para testar
$topics = [
    "S2 vibracao_motor",
    "S2_vibracao_motor",
    "vibracao_motor",
    "#"  // Wildcard para receber tudo
];

echo "=== TESTE DE CONEXÃO MQTT ===\n";
echo "Servidor: $server:$port\n";
echo "Testando múltiplos tópicos...\n";
echo "Arquivo CA: $cafile\n";
echo "Arquivo existe? " . (file_exists($cafile) ? "SIM" : "NÃO") . "\n\n";

$messages = [];

try {
    $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);
    $mqtt->debug = true; // Ativa debug
    $mqtt->cafile = $cafile;
    
    echo "Tentando conectar...\n";
    if (!$mqtt->connect(true, NULL, $username, $password)) {
        echo "ERRO: Não foi possível conectar ao broker\n";
        exit;
    }
    
    echo "Conectado com sucesso!\n";
    echo "Assinando tópicos:\n";
    
    $topics_config = [];
    foreach ($topics as $topic) {
        echo "  - $topic\n";
        $topics_config[$topic] = [
            "qos" => 0,
            "function" => function ($topic, $msg) use (&$messages) {
                echo "\n>>> MENSAGEM RECEBIDA <<<\n";
                echo "Tópico: '$topic'\n";
                echo "Mensagem: '$msg'\n";
                $messages[] = ['topic' => $topic, 'msg' => $msg];
            }
        ];
    }
    
    $mqtt->subscribe($topics_config, 0);
    
    echo "\nAguardando mensagens por 10 segundos...\n";
    echo "PUBLIQUE AGORA no broker!\n\n";
    
    $start = time();
    while (time() - $start < 10) {
        $mqtt->proc();
        usleep(100000); // 0.1 segundo
    }
    
    $mqtt->close();
    
    echo "\n=== RESULTADO ===\n";
    if (!empty($messages)) {
        echo "Total de mensagens recebidas: " . count($messages) . "\n";
        foreach ($messages as $i => $m) {
            echo ($i+1) . ". Tópico: '{$m['topic']}' | Mensagem: '{$m['msg']}'\n";
        }
    } else {
        echo "Nenhuma mensagem recebida\n";
        echo "\nVERIFIQUE:\n";
        echo "1. O tópico está correto no dispositivo?\n";
        echo "2. O dispositivo está conectado ao mesmo broker?\n";
        echo "3. Você publicou DURANTE os 10 segundos de escuta?\n";
    }
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
