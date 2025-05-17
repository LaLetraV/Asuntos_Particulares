<?php

// Iniciar sesión para mantener al usuario logueado
session_start();

// Comprobar que la sesión de usuario es correcta en las páginas protegidas
if (str_contains($_SERVER['REQUEST_URI'], 'app')) {
    require_once 'database.php';
    require_once 'user.php';

    $username = $_SESSION['username'];
    $token = $_SESSION['token'];
    $user = fetch_user_by_name($username);

    if (!$user || $user['contrasena'] != $token) {
        header("Location: /asuntos_particulares");
        exit();
    }

    return $user;
}

?>