<?php

function getDashboardUrl()
{
    $tipo = $_SESSION['tipo'] ?? 'normal';
    return ($tipo === 'admin') ? 'dashboard.php' : 'dashboard_funcionario.php';
}

function isAdmin()
{
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin';
}

function redirectToDashboard()
{
    header('Location: ' . getDashboardUrl());
    exit();
}

