<?php
// Conexão de autenticação (modelo solicitado)
// Banco: login_db | Tabela: usuarios (pk, username UNIQUE, senha, cargo ENUM('adm','func'))

$mysqli = @new mysqli("localhost", "root", "root", "login_db");
if ($mysqli->connect_errno) {
    die("Erro de conexão: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

// Opcional: helper para obter a conexão quando não usando variável global
function auth_db(): mysqli {
    global $mysqli;
    return $mysqli;
}
