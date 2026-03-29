<?php
// backoffice/src/includes/auth.php
// Inclure EN PREMIER dans chaque page protégée

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: /login.php');
    exit;
}

$currentUser = $_SESSION['user'];