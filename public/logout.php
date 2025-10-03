<?php
// logout
session_start();
if(isset($_GET['logout'])){
    session_destroy();
    header('Location: login.php');
    exit;
}
header('Location: login.php');
exit;