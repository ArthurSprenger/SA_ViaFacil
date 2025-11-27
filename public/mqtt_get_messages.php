<?php
require(__DIR__ . '/../includes/phpMQTT.php');

$server = "7aecec580ecf4e5cbac2d52b35eb85b9.s1.eu.hivemq.cloud";
$port = 8883;
$topic = $_GET['topic'] ?? "TESTEMQTT";
$client_id = "phpmqtt-" . rand();

$username = "Henry";
$password = "HenryDSM2";
$cafile = __DIR__ . "/../config/certs/cacert.pem";
$message = "";

$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);
$mqtt->cafile = $cafile;

if (!$mqtt->connect(true, NULL, $username, $password)) {
    echo "Erro ao conectar";
    exit;
}

// Subscribing e coletando mensagens por 1-2 segundos
$mqtt->subscribe([
    $topic => [
        "qos" => 0,
        "function" => function ($topic, $msg) use (&$message) {
            if (!empty($msg)) {
                $message = $msg;
            }
        }
    ]
], 0);

$start = time();
while (time() - $start < 2) { // escuta 2 segundos
    $mqtt->proc();
}

$mqtt->close();

echo $message;
