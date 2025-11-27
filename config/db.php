<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sa_viafacil_db');
define('DB_USER', 'root');
define('DB_PASS', ''); 

function db_connect(): mysqli {
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die('Falha na conexão: ' . $conn->connect_error);
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
