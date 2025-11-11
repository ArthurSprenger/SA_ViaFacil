<?php
session_start();
if(!isset($_SESSION['usuario_id'])){
  header('Location: login.php');
  exit;
}

$destinoAdmin = 'dashboard.php#avisos';
$destinoFuncionario = 'aviso_funcionario.php';

if(isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin'){
  header('Location: ' . $destinoAdmin);
  exit;
}

header('Location: ' . $destinoFuncionario);
exit;
