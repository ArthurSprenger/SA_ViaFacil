<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../includes/avisos_service.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'Não autenticado']);
    exit;
}

$conn = db_connect();
ensureAvisosSchema($conn);

$tipoUsuario = $_SESSION['tipo'] ?? 'normal';
$isAdmin = $tipoUsuario === 'admin';

$where = [];
if ($isAdmin) {
    $where[] = "a.status IN ('ativo','encerrado')";
} else {
    $destinosPermitidos = "'todos','funcionarios'";
    $where[] = "a.status = 'ativo'";
    $where[] = '(a.expira_em IS NULL OR a.expira_em > NOW())';
    $where[] = "a.destino IN ($destinosPermitidos)";
}

$sql = "SELECT a.id, a.titulo, a.mensagem, a.tipo, a.destino, a.status, a.expira_em, a.encerrado_em, a.solicitacao_id,
               DATE_FORMAT(a.criado_em, '%d/%m/%Y %H:%i') AS data_formatada,
               DATE_FORMAT(a.expira_em, '%d/%m/%Y %H:%i') AS expira_formatada,
               u.nome AS autor
        FROM avisos a
        INNER JOIN usuarios u ON a.usuario_id = u.id";

if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= " ORDER BY CASE WHEN a.status = 'ativo' THEN 0 ELSE 1 END, a.criado_em DESC LIMIT 100";

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