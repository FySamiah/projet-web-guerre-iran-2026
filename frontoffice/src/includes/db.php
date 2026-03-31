<?php
// frontoffice/src/includes/db.php
// Connexion & Fonctions de lecture seule

$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'db';
$db   = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'guerre_iran';
$user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'user';
$pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: 'password';

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
    die('Erreur connexion BDD : ' . $e->getMessage());
}

// ══════════════════════════════════════════
//  ARTICLES (lecture seule)
// ══════════════════════════════════════════

/**
 * Récupère tous les articles publiés (triés par date décroissante)
 */
function getArticles(): array {
    global $pdo;
    $st = $pdo->query(
        "SELECT id, titre, slug, resume, image, alt_image, categorie_id, 
                date_publication, statut
         FROM articles 
         WHERE statut = 'publie' 
         AND date_publication <= NOW()
         ORDER BY date_publication DESC"
    );
    return $st->fetchAll() ?: [];
}

/**
 * Récupère un article par son slug
 */
function getArticleBySlug(string $slug): ?array {
    global $pdo;
    $st = $pdo->prepare(
        "SELECT a.*, c.nom as categorie, u.nom as auteur
         FROM articles a
         LEFT JOIN categories c ON a.categorie_id = c.id
         LEFT JOIN users u ON a.user_id = u.id
         WHERE a.slug = ? AND a.statut = 'publie' AND a.date_publication <= NOW()"
    );
    $st->execute([$slug]);
    $article = $st->fetch();
    return $article ?: null;
}

/**
 * Récupère les articles d'une catégorie
 */
function getArticlesByCategory(int $categoryId, int $limit = 20): array {
    global $pdo;
    $st = $pdo->prepare(
        "SELECT id, titre, slug, resume, image, alt_image, categorie_id,
                date_publication, statut
         FROM articles
         WHERE categorie_id = ? AND statut = 'publie' AND date_publication <= NOW()
         ORDER BY date_publication DESC
         LIMIT ?"
    );
    $st->execute([$categoryId, $limit]);
    return $st->fetchAll() ?: [];
}

/**
 * Récupère les articles récents (pour widget)
 */
function getRecentArticles(int $limit = 5): array {
    global $pdo;
    $st = $pdo->prepare(
        "SELECT id, titre, slug, resume, image, alt_image, categorie_id,
                date_publication, statut
         FROM articles
         WHERE statut = 'publie' AND date_publication <= NOW()
         ORDER BY date_publication DESC
         LIMIT ?"
    );
    $st->execute([$limit]);
    return $st->fetchAll() ?: [];
}

// ══════════════════════════════════════════
//  CATEGORIES (lecture seule)
// ══════════════════════════════════════════

/**
 * Récupère toutes les catégories avec nombre d'articles
 */
function getCategories(): array {
    global $pdo;
    $st = $pdo->query(
        "SELECT c.id, c.nom, c.slug, c.description,
                COUNT(a.id) as nb_articles
         FROM categories c
         LEFT JOIN articles a ON c.id = a.categorie_id 
                AND a.statut = 'publie' 
                AND a.date_publication <= NOW()
         GROUP BY c.id
         ORDER BY c.nom ASC"
    );
    return $st->fetchAll() ?: [];
}

/**
 * Récupère une catégorie par son slug
 */
function getCategoryBySlug(string $slug): ?array {
    global $pdo;
    $st = $pdo->prepare(
        "SELECT id, nom, slug, description FROM categories WHERE slug = ?"
    );
    $st->execute([$slug]);
    return $st->fetch() ?: null;
}

/**
 * Récupère une catégorie par son ID
 */
function getCategoryById(int $id): ?array {
    global $pdo;
    $st = $pdo->prepare(
        "SELECT id, nom, slug, description FROM categories WHERE id = ?"
    );
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

// ══════════════════════════════════════════
//  HELPERS
// ══════════════════════════════════════════

/**
 * Slugify un texte
 */
function slugify(string $text): string {
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}

/**
 * Formate une date
 */
function formatDate(string $date, string $format = 'd/m/Y'): string {
    return date($format, strtotime($date));
}

/**
 * Tronque un texte
 */
function truncate(string $text, int $length = 150): string {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '…';
}
