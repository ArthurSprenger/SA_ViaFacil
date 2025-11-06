<?php

require_once __DIR__ . '/includes/phpMQTT.php';

$server = "broker.hivemq.com";
$port = 1883;
$client_id = "viafacil_publisher_" . uniqid();

$sensores = [
    [
        'tipo' => 'temperatura_freio',
        'unidade' => '°C',
        'min' => 60,
        'max' => 120
    ],
    [
        'tipo' => 'vibracao_motor',
        'unidade' => 'mm/s',
        'min' => 1.5,
        'max' => 5.0
    ],
    [
        'tipo' => 'pressao_ar',
        'unidade' => 'bar',
        'min' => 7.0,
        'max' => 9.5
    ],
    [
        'tipo' => 'temperatura_motor',
        'unidade' => '°C',
        'min' => 70,
        'max' => 110
    ]
];

echo "[" . date('Y-m-d H:i:s') . "] Iniciando Simulador MQTT...\n";
echo "[" . date('Y-m-d H:i:s') . "] Broker: $server:$port\n";
echo str_repeat("-", 60) . "\n";

$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

if (!$mqtt->connect(true, NULL, "", "")) {
    die("[ERRO] Não foi possível conectar ao broker MQTT\n");
}

echo "[" . date('Y-m-d H:i:s') . "] ✓ Conectado ao broker!\n";
echo "[" . date('Y-m-d H:i:s') . "] Publicando dados a cada 5 segundos... (Ctrl+C para sair)\n";
echo str_repeat("-", 60) . "\n";

$contador = 0;

while (true) {
    $contador++;
    
    foreach ($sensores as $sensor) {
        $valor = round(
            $sensor['min'] + (($sensor['max'] - $sensor['min']) * (rand(0, 100) / 100)),
            2
        );
        
        $payload = json_encode([
            'tipo' => $sensor['tipo'],
            'valor' => $valor,
            'unidade' => $sensor['unidade'],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
        $topic = "viafacil/sensores/" . $sensor['tipo'];
        
        $mqtt->publish($topic, $payload, 0);
        
        echo "[" . date('H:i:s') . "] [$contador] Publicado: {$sensor['tipo']} = $valor {$sensor['unidade']}\n";
    }
    
    echo str_repeat("-", 60) . "\n";
    
    sleep(5);
}

$mqtt->close();
