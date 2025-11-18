<?php

require_once __DIR__ . '/includes/phpMQTT.php';
require_once __DIR__ . '/config/db.php';

set_time_limit(0);

function loadEnvFile(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || $trimmed[0] === '#') {
            continue;
        }

        $parts = explode('=', $trimmed, 2);
        $key = trim($parts[0]);
        $value = trim($parts[1] ?? '');
        $value = trim($value, "\"' ");

        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
    }
}

function env(string $key, $default = null)
{
    $value = getenv($key);
    return ($value === false || $value === null) ? $default : $value;
}

loadEnvFile(__DIR__ . '/.env');

$mqttConfig = [
    'broker' => env('MQTT_BROKER', 'broker.hivemq.com'),
    'port' => (int)env('MQTT_PORT', 8883),
    'username' => env('MQTT_USERNAME', ''),
    'password' => env('MQTT_PASSWORD', ''),
    'tls_enabled' => filter_var(env('MQTT_TLS_ENABLED', 'true'), FILTER_VALIDATE_BOOLEAN),
    'tls_cafile' => env('MQTT_TLS_CAFILE') ?: __DIR__ . '/config/certs/DigiCertGlobalRootCA.crt.pem',
    'client_id' => env('MQTT_CLIENT_ID', 'viafacil_subscriber') . '_' . uniqid(),
    'wildcard_topic' => env('MQTT_TOPIC', 'viafacil/sensores/#'),
    'topics_list' => array_filter(array_map('trim', explode(',', str_replace('"', '', env('MQTT_TOPICS', '')))))
];

$sensorTopicMap = [
    'S1 umidade' => ['tipo' => 's1_umidade', 'unidade' => '%', 'parser' => 'parseNumericPayload'],
    'S1 temperatura' => ['tipo' => 's1_temperatura', 'unidade' => '°C', 'parser' => 'parseNumericPayload'],
    'S1 iluminacao' => ['tipo' => 's1_iluminacao', 'unidade' => 'lux', 'parser' => 'parseNumericPayload'],
    'Projeto S2 Distancia1' => ['tipo' => 's2_distancia1', 'unidade' => 'cm', 'parser' => 'parseNumericPayload'],
    'Projeto S2 Distancia2' => ['tipo' => 's2_distancia2', 'unidade' => 'cm', 'parser' => 'parseNumericPayload'],
    'Projeto S3 Presenca3' => ['tipo' => 's3_presenca', 'unidade' => 'status', 'parser' => 'parsePresencePayload'],
    'Projeto S3 Ultrassom3' => ['tipo' => 's3_ultrassom', 'unidade' => 'cm', 'parser' => 'parseNumericPayload'],
    'projeto trem velocidade' => ['tipo' => 'trem_velocidade', 'unidade' => 'km/h', 'parser' => 'parseNumericPayload'],
];

function parseNumericPayload(string $payload): ?float
{
    if (preg_match('/-?\d+(?:[\.,]\d+)?/', $payload, $matches)) {
        $value = str_replace(',', '.', $matches[0]);
        return is_numeric($value) ? (float)$value : null;
    }
    return null;
}

function parsePresencePayload(string $payload): ?float
{
    $normalized = strtolower(trim($payload));
    return match ($normalized) {
        '1', 'true', 'on', 'ativo', 'detectado' => 1.0,
        '0', 'false', 'off', 'inativo', 'livre' => 0.0,
        default => parseNumericPayload($payload)
    };
}

function extractMeasurement(string $topic, string $payload, array $map): array
{
    $decoded = json_decode($payload, true);
    if (is_array($decoded) && isset($decoded['tipo'], $decoded['valor'])) {
        return [
            'tipo' => (string)$decoded['tipo'],
            'valor' => (float)$decoded['valor'],
            'unidade' => $decoded['unidade'] ?? ''
        ];
    }

    if (!isset($map[$topic])) {
        throw new InvalidArgumentException("Tópico '{$topic}' não está mapeado e payload não é JSON válido");
    }

    $config = $map[$topic];
    $parser = $config['parser'];
    $valor = $parser($payload);

    if ($valor === null) {
        throw new InvalidArgumentException("Não foi possível interpretar valor numérico para o tópico '{$topic}'");
    }

    return [
        'tipo' => $config['tipo'],
        'valor' => $valor,
        'unidade' => $config['unidade'] ?? ''
    ];
}

$topicsToSubscribe = $mqttConfig['topics_list'];
if (empty($topicsToSubscribe)) {
    $topicsToSubscribe[] = $mqttConfig['wildcard_topic'];
}

echo "[" . date('Y-m-d H:i:s') . "] Iniciando cliente MQTT assinante...\n";
echo "[" . date('Y-m-d H:i:s') . "] Broker: {$mqttConfig['broker']}:{$mqttConfig['port']}\n";
echo "[" . date('Y-m-d H:i:s') . "] Tópicos: " . implode(', ', $topicsToSubscribe) . "\n";
echo str_repeat('-', 60) . "\n";

$conn = db_connect();

$cafile = $mqttConfig['tls_enabled'] ? $mqttConfig['tls_cafile'] : null;
$mqtt = new Bluerhinos\phpMQTT($mqttConfig['broker'], $mqttConfig['port'], $mqttConfig['client_id'], $cafile);

$username = $mqttConfig['username'] ?? '';
$password = $mqttConfig['password'] ?? '';

if (!$mqtt->connect(true, null, $username, $password)) {
    die("[ERRO] Não foi possível conectar ao broker MQTT\n");
}

echo "[" . date('Y-m-d H:i:s') . "] ✓ Conectado ao broker com sucesso!\n";

$callback = function ($topic, $msg) use ($conn, $sensorTopicMap) {
    $timestamp = date('Y-m-d H:i:s');
    echo "\n[$timestamp] Nova mensagem recebida!\n";
    echo "  Tópico: $topic\n";
    echo "  Payload: $msg\n";

    try {
        $measurement = extractMeasurement($topic, (string)$msg, $sensorTopicMap);
    } catch (InvalidArgumentException $e) {
        echo "  [ERRO] " . $e->getMessage() . "\n";
        return;
    }

    $tipo = $measurement['tipo'];
    $valor = $measurement['valor'];
    $unidade = $measurement['unidade'] ?: 'valor';

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
};

$subscriptions = [];
foreach ($topicsToSubscribe as $topic) {
    $subscriptions[$topic] = [
        'qos' => 0,
        'function' => $callback
    ];
}

$mqtt->subscribe($subscriptions, 0);

echo "[" . date('Y-m-d H:i:s') . "] ✓ Inscrito nos tópicos configurados!\n";
echo "[" . date('Y-m-d H:i:s') . "] Aguardando mensagens... (Ctrl+C para sair)\n";
echo str_repeat('-', 60) . "\n";

while (true) {
    $mqtt->proc();
    usleep(100000);
}

$mqtt->close();
