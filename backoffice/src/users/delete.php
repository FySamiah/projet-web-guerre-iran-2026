<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Seuls les admins peuvent gerer les utilisateurs
if ($currentUser['role'] !== 'admin') {
    header('Location: /dashboard.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);

// Ne pas se supprimer soi-meme
if ($id === $currentUser['id']) {
    header('Location: /users/list.php?error=self_delete');
    exit;
}

$user = getUserById($id);
if (!$user) {
    header('Location: /users/list.php?error=not_found');
    exit;
}

if (deleteUser($id)) {
    header('Location: /users/list.php?success=deleted');
} else {
    header('Location: /users/list.php?error=last_admin');
}
exit;
