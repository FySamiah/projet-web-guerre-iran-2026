<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Seuls les admins peuvent gerer les utilisateurs
if ($currentUser['role'] !== 'admin') {
    header('Location: /admin/dashboard.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);

// Ne pas se supprimer soi-meme
if ($id === $currentUser['id']) {
    header('Location: /admin/users/list.php?error=self_delete');
    exit;
}

$user = getUserById($id);
if (!$user) {
    header('Location: /admin/users/list.php?error=not_found');
    exit;
}

if (deleteUser($id)) {
    header('Location: /admin/users/list.php?success=deleted');
} else {
    header('Location: /admin/users/list.php?error=last_admin');
}
exit;
