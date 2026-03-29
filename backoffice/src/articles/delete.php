<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$id      = (int)($_GET['id'] ?? 0);
$article = getArticleById($id);

if (!$article) { header('Location: /articles/list.php?error=notfound'); exit; }

if ($article['image']) {
    $path = __DIR__ . '/../uploads/' . $article['image'];
    if (file_exists($path)) unlink($path);
}

deleteArticle($id)
    ? header('Location: /articles/list.php?success=deleted')
    : header('Location: /articles/list.php?error=delete');
exit;