<?php
// backoffice/src/includes/db.php

$host = getenv('DB_HOST') ?: 'db';
$db   = getenv('DB_NAME') ?: 'guerre_iran';
$user = getenv('DB_USER') ?: 'user';
$pass = getenv('DB_PASS') ?: 'password';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user, $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Connexion BDD échouée : ' . $e->getMessage());
}

// ══════════════════════════════════════════
//  USERS
// ══════════════════════════════════════════

function getUserByEmail(string $email): ?array {
    global $pdo;
    $st = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $st->execute([$email]);
    return $st->fetch() ?: null;
}

function getUsers(): array {
    global $pdo;
    return $pdo->query(
        "SELECT id, email, role, created_at FROM users ORDER BY created_at DESC"
    )->fetchAll();
}

// ══════════════════════════════════════════
//  CATÉGORIES
// ══════════════════════════════════════════

function getCategories(): array {
    global $pdo;
    return $pdo->query(
        "SELECT c.*, COUNT(a.id) AS nb_articles
         FROM categories c
         LEFT JOIN articles a ON a.categorie_id = c.id
         GROUP BY c.id
         ORDER BY c.nom ASC"
    )->fetchAll();
}

function getCategorieById(int $id): ?array {
    global $pdo;
    $st = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

function createCategorie(array $data): bool {
    global $pdo;
    $st = $pdo->prepare(
        "INSERT INTO categories (nom, slug, description) VALUES (?, ?, ?)"
    );
    return $st->execute([$data['nom'], $data['slug'], $data['description'] ?? '']);
}

function updateCategorie(int $id, array $data): bool {
    global $pdo;
    $st = $pdo->prepare(
        "UPDATE categories SET nom = ?, slug = ?, description = ? WHERE id = ?"
    );
    return $st->execute([$data['nom'], $data['slug'], $data['description'] ?? '', $id]);
}

function deleteCategorie(int $id): bool {
    global $pdo;
    $st = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE categorie_id = ?");
    $st->execute([$id]);
    if ($st->fetchColumn() > 0) return false;
    $st = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    return $st->execute([$id]);
}

// ══════════════════════════════════════════
//  ARTICLES
// ══════════════════════════════════════════

function getArticles(): array {
    global $pdo;
    return $pdo->query(
        "SELECT a.*, c.nom AS categorie, u.email AS auteur
         FROM articles a
         LEFT JOIN categories c ON a.categorie_id = c.id
         LEFT JOIN users      u ON a.user_id      = u.id
         ORDER BY a.date_publication DESC"
    )->fetchAll();
}

function getArticleById(int $id): ?array {
    global $pdo;
    $st = $pdo->prepare(
        "SELECT a.*, c.nom AS categorie
         FROM articles a
         LEFT JOIN categories c ON a.categorie_id = c.id
         WHERE a.id = ?"
    );
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

function createArticle(array $data): bool {
    global $pdo;
    $st = $pdo->prepare(
        "INSERT INTO articles
         (titre, slug, contenu, resume, image, alt_image,
          categorie_id, user_id, statut)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    return $st->execute([
        $data['titre'],
        $data['slug'],
        $data['contenu'],
        $data['resume']      ?? '',
        $data['image']       ?? null,
        $data['alt_image']   ?? '',
        $data['categorie_id'],
        $data['user_id'],
        $data['statut'],
    ]);
}

function updateArticle(int $id, array $data): bool {
    global $pdo;
    $st = $pdo->prepare(
        "UPDATE articles
         SET titre = ?, slug = ?, contenu = ?, resume = ?,
             image = ?, alt_image = ?, categorie_id = ?, statut = ?
         WHERE id = ?"
    );
    return $st->execute([
        $data['titre'],
        $data['slug'],
        $data['contenu'],
        $data['resume']      ?? '',
        $data['image']       ?? null,
        $data['alt_image']   ?? '',
        $data['categorie_id'],
        $data['statut'],
        $id,
    ]);
}

function deleteArticle(int $id): bool {
    global $pdo;
    $st = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    return $st->execute([$id]);
}

function countArticles(): array {
    global $pdo;
    $total     = (int)$pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    $publie    = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE statut='publie'")->fetchColumn();
    $brouillon = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE statut='brouillon'")->fetchColumn();
    return compact('total', 'publie', 'brouillon');
}

// ══════════════════════════════════════════
//  UTILITAIRES
// ══════════════════════════════════════════

function slugify(string $text): string {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    return strtolower(trim($text, '-'));
}

function uploadImage(array $file): ?string {
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo   = finfo_open(FILEINFO_MIME_TYPE);
    $mime    = finfo_file($finfo, $file['tmp_name']);
    if (!in_array($mime, $allowed))          return null;
    if ($file['size'] > 5 * 1024 * 1024)    return null;
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid('img_', true) . '.' . $ext;
    $dest     = __DIR__ . '/../uploads/' . $filename;
    return move_uploaded_file($file['tmp_name'], $dest) ? $filename : null;
}