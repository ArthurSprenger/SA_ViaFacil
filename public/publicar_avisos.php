<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['tipo'] ?? 'normal') !== 'admin') {
    header('Location: login.php');
    exit;
}

header('Location: dashboard.php#avisos');
exit;
