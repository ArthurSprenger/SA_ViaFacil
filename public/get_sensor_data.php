<?php
header('Content-Type: application/json');
require_once __DIR__.'/../config/db.php';

$conn = db_connect();

$sql = "
  SELECT 
    sd.id_sensor,
    sd.valor,
    sd.unidade,
    DATE_FORMAT(sd.data_hora, '%d/%m/%Y %H:%i:%s') as data_hora,
    s.tipo
  FROM sensor_data sd
  INNER JOIN sensor s ON sd.id_sensor = s.id
  WHERE sd.id IN (
    SELECT MAX(id) 
    FROM sensor_data 
    GROUP BY id_sensor
  )
  ORDER BY s.tipo ASC
";

$result = $conn->query($sql);

$dados = [];
if ($result) {
  while($row = $result->fetch_assoc()) {
    $dados[] = $row;
  }
}

echo json_encode($dados);
$conn->close();
