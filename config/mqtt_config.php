<?php
// Configurações do broker MQTT
define('MQTT_SERVER', 'ef339175de264ab783f4bea1e2a1abe9.s1.eu.hivemq.cloud');
define('MQTT_PORT', 8883);

// Credenciais dos dispositivos (você pode usar qualquer uma delas)
define('MQTT_USERNAME', 'Pedro');  // ou 'felipe', 'Henry'
define('MQTT_PASSWORD', 'PedroDSM2');  // ou 'FelipeDSM2', 'HenryDSM2'

// Tópicos MQTT baseados nos dispositivos reais
define('MQTT_TOPICS', [
    // S1 - DHT11 + LDR
    'S1 umidade' => ['sensor_id' => 1, 'tipo' => 'umidade', 'unidade' => '%'],
    'S1 temperatura' => ['sensor_id' => 2, 'tipo' => 'temperatura', 'unidade' => '°C'],
    'S1 iluminacao' => ['sensor_id' => 3, 'tipo' => 'iluminacao', 'unidade' => 'lux'],
    
    // S2 - Sensores ultrassônicos
    'Projeto S2 Distancia1' => ['sensor_id' => 4, 'tipo' => 'distancia1', 'unidade' => 'cm'],
    'Projeto S2 Distancia2' => ['sensor_id' => 5, 'tipo' => 'distancia2', 'unidade' => 'cm'],
    
    // S3 - Ultrassom + Presença
    'Projeto S3 Presenca3' => ['sensor_id' => 6, 'tipo' => 'presenca', 'unidade' => 'bool'],
    'Projeto S3 Ultrassom3' => ['sensor_id' => 7, 'tipo' => 'ultrassom', 'unidade' => 'cm'],
    
    // Trem - Velocidade
    'projeto trem velocidade' => ['sensor_id' => 8, 'tipo' => 'velocidade', 'unidade' => 'km/h'],
]);

define('MQTT_CLIENT_ID_PREFIX', 'viafacil_php_');
define('MQTT_CAFILE', __DIR__ . '/certs/cacert.pem');

