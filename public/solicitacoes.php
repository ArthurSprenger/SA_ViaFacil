<?php
session_start();
if(isset($_SESSION['tipo']) && $_SESSION['tipo']==='admin'){
  header('Location: dashboard.php#solicitacoes');
} else {
  header('Location: solicitacoes_funcionario.php');
}
exit;
