<?php
require_once __DIR__ . '/phpMQTT.php';

function publicarNotificacao($tipo, $titulo, $mensagem, $usuario_remetente_id, $usuario_destinatario_id = null) {
    $server = "broker.hivemq.com";
    $port = 1883;
    $client_id = "viafacil_publisher_" . uniqid();
    
    $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);
    
    if (!$mqtt->connect(true, NULL, "", "")) {
        return false;
    }
    
    $payload = json_encode([
        'tipo' => $tipo,
        'titulo' => $titulo,
        'mensagem' => $mensagem,
        'usuario_remetente_id' => $usuario_remetente_id,
        'usuario_destinatario_id' => $usuario_destinatario_id,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
    $topic = "viafacil/notificacoes/" . $tipo;
    
    $mqtt->publish($topic, $payload, 0);
    $mqtt->close();
    
    return true;
}
?>
