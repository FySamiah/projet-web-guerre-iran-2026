<?php
// backoffice/src/index.php
// Point d'entrée principal — redirige selon la route

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si non connecté, toujours rediriger vers login
if (!isset($_SESSION['user'])) {
    header('Location: /login.php');
    exit;
}

// Rediriger vers le dashboard par défaut
header('Location: /dashboard.php');
exit;