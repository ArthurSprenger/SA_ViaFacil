<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__.'/../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'Não autenticado']);
    exit;
}

$conn = db_connect();

$sql = "SELECT a.*, u.nome as autor, DATE_FORMAT(a.criado_em, '%d/%m/%Y %H:%i') as data_formatada 
        FROM avisos a 
        INNER JOIN usuarios u ON a.usuario_id = u.id 
        ORDER BY a.criado_em DESC 
        LIMIT 50";

$result = $conn->query($sql);

$avisos = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $avisos[] = $row;
    }
}

echo json_encode($avisos);
$conn->close();
?>