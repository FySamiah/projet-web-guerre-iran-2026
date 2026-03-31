<?php
// backoffice/src/categories/delete.php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header('Location: /admin/categories/list.php?error=invalid');
    exit;
}

$ok = deleteCategorie($id);

if ($ok) {
    header('Location: /admin/categories/list.php?success=deleted');
} else {
    // La fonction retourne false si des articles sont liés
    header('Location: /admin/categories/list.php?error=has_articles');
}
exit;