<?php

class Auth
{
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public function loginUser($user)
    {
        // Compat: set both keys used across the app
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['username'] = $user['nome'];
        $_SESSION['tipo'] = $user['tipo'] ?? 'normal';
    }

    public function logout()
    {
        session_destroy();
        header('Location: login.php');
        exit();
    }
}

