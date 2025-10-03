<?php
// PDO connection based on provided sample (banco_sabado) but adapted to current DB name
$servername = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'sa_viafacil_db';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Conexão falhou, erro: ' . $e->getMessage());
}
