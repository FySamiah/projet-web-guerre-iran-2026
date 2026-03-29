<?php
// backoffice/src/includes/db.php

$host = $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?: 'db';
$db   = $_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? getenv('DB_NAME') ?: 'guerre_iran';
$user = $_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? getenv('DB_USER') ?: 'user';
$pass = $_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? getenv('DB_PASS') ?: 'password';

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
    die('Connexion BDD echouee : ' . $e->getMessage());
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
        "SELECT id, nom, email, role, actif, created_at FROM users ORDER BY created_at DESC"
    )->fetchAll();
}

function getUserById(int $id): ?array {
    global $pdo;
    $st = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

function createUser(array $data): bool {
    global $pdo;
    $st = $pdo->prepare(
        "INSERT INTO users (nom, email, mot_de_passe, role, actif) VALUES (?, ?, ?, ?, ?)"
    );
    return $st->execute([
        $data['nom'],
        $data['email'],
        password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
        $data['role'],
        $data['actif'] ?? 1,
    ]);
}

function updateUser(int $id, array $data): bool {
    global $pdo;
    if (!empty($data['mot_de_passe'])) {
        $st = $pdo->prepare(
            "UPDATE users SET nom = ?, email = ?, mot_de_passe = ?, role = ?, actif = ? WHERE id = ?"
        );
        return $st->execute([
            $data['nom'],
            $data['email'],
            password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
            $data['role'],
            $data['actif'],
            $id,
        ]);
    } else {
        $st = $pdo->prepare(
            "UPDATE users SET nom = ?, email = ?, role = ?, actif = ? WHERE id = ?"
        );
        return $st->execute([
            $data['nom'],
            $data['email'],
            $data['role'],
            $data['actif'],
            $id,
        ]);
    }
}

function deleteUser(int $id): bool {
    global $pdo;
    // Ne pas supprimer le dernier admin
    $st = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin' AND id != ?");
    $st->execute([$id]);
    if ($st->fetchColumn() < 1) {
        $user = getUserById($id);
        if ($user && $user['role'] === 'admin') return false;
    }
    $st = $pdo->prepare("DELETE FROM users WHERE id = ?");
    return $st->execute([$id]);
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

function getArticlesFiltered(array $filters): array {
    global $pdo;

    $sql = "SELECT a.*, c.nom AS categorie, u.email AS auteur
            FROM articles a
            LEFT JOIN categories c ON a.categorie_id = c.id
            LEFT JOIN users u ON a.user_id = u.id
            WHERE 1=1";
    $params = [];

    // Recherche par mot-clé (titre ou contenu)
    if (!empty($filters['search'])) {
        $sql .= " AND (a.titre LIKE ? OR a.contenu LIKE ?)";
        $params[] = '%' . $filters['search'] . '%';
        $params[] = '%' . $filters['search'] . '%';
    }

    // Filtre par catégorie
    if (!empty($filters['categorie_id'])) {
        $sql .= " AND a.categorie_id = ?";
        $params[] = (int)$filters['categorie_id'];
    }

    // Filtre par statut
    if (!empty($filters['statut'])) {
        $sql .= " AND a.statut = ?";
        $params[] = $filters['statut'];
    }

    // Filtre par auteur
    if (!empty($filters['user_id'])) {
        $sql .= " AND a.user_id = ?";
        $params[] = (int)$filters['user_id'];
    }

    // Filtre par date (depuis)
    if (!empty($filters['date_from'])) {
        $sql .= " AND DATE(a.date_publication) >= ?";
        $params[] = $filters['date_from'];
    }

    // Filtre par date (jusqu'à)
    if (!empty($filters['date_to'])) {
        $sql .= " AND DATE(a.date_publication) <= ?";
        $params[] = $filters['date_to'];
    }

    $sql .= " ORDER BY a.date_publication DESC";

    $st = $pdo->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
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
          categorie_id, user_id, statut, date_publication)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    return $st->execute([
        $data['titre'],
        $data['slug'],
        $data['contenu'],
        $data['resume']           ?? '',
        $data['image']            ?? null,
        $data['alt_image']        ?? '',
        $data['categorie_id'],
        $data['user_id'],
        $data['statut'],
        $data['date_publication'] ?? date('Y-m-d H:i:s'),
    ]);
}

function updateArticle(int $id, array $data): bool {
    global $pdo;
    $st = $pdo->prepare(
        "UPDATE articles
         SET titre = ?, slug = ?, contenu = ?, resume = ?,
             image = ?, alt_image = ?, categorie_id = ?, statut = ?, date_publication = ?
         WHERE id = ?"
    );
    return $st->execute([
        $data['titre'],
        $data['slug'],
        $data['contenu'],
        $data['resume']           ?? '',
        $data['image']            ?? null,
        $data['alt_image']        ?? '',
        $data['categorie_id'],
        $data['statut'],
        $data['date_publication'] ?? date('Y-m-d H:i:s'),
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
    $planifie  = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE statut='planifie'")->fetchColumn();
    return compact('total', 'publie', 'brouillon', 'planifie');
}

function getArticlesByMonth(): array {
    global $pdo;
    $sql = "SELECT DATE_FORMAT(date_publication, '%Y-%m') AS mois, COUNT(*) AS nb
            FROM articles
            WHERE date_publication >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY mois
            ORDER BY mois ASC";
    return $pdo->query($sql)->fetchAll();
}

function getArticlesByCategory(): array {
    global $pdo;
    $sql = "SELECT c.nom, COUNT(a.id) AS nb
            FROM categories c
            LEFT JOIN articles a ON a.categorie_id = c.id
            GROUP BY c.id, c.nom
            ORDER BY nb DESC";
    return $pdo->query($sql)->fetchAll();
}

function countUsers(): int {
    global $pdo;
    return (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
}

function countMedias(): int {
    global $pdo;
    return (int)$pdo->query("SELECT COUNT(*) FROM medias")->fetchColumn();
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

// ══════════════════════════════════════════
//  MÉDIATHÈQUE
// ══════════════════════════════════════════

function getMedias(): array {
    global $pdo;
    return $pdo->query(
        "SELECT m.*, u.email AS auteur
         FROM medias m
         LEFT JOIN users u ON m.user_id = u.id
         ORDER BY m.created_at DESC"
    )->fetchAll();
}

function getMediaById(int $id): ?array {
    global $pdo;
    $st = $pdo->prepare("SELECT * FROM medias WHERE id = ?");
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

function createMedia(array $data): bool {
    global $pdo;
    $st = $pdo->prepare(
        "INSERT INTO medias (filename, original_name, alt_text, mime_type, size, user_id)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    return $st->execute([
        $data['filename'],
        $data['original_name'],
        $data['alt_text'] ?? '',
        $data['mime_type'],
        $data['size'],
        $data['user_id'],
    ]);
}

function updateMedia(int $id, array $data): bool {
    global $pdo;
    $st = $pdo->prepare("UPDATE medias SET alt_text = ? WHERE id = ?");
    return $st->execute([$data['alt_text'], $id]);
}

function deleteMedia(int $id): bool {
    global $pdo;
    $media = getMediaById($id);
    if ($media) {
        $filepath = __DIR__ . '/../uploads/' . $media['filename'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
    $st = $pdo->prepare("DELETE FROM medias WHERE id = ?");
    return $st->execute([$id]);
}

function uploadMedia(array $file, int $userId): ?array {
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo   = finfo_open(FILEINFO_MIME_TYPE);
    $mime    = finfo_file($finfo, $file['tmp_name']);
    if (!in_array($mime, $allowed))       return null;
    if ($file['size'] > 5 * 1024 * 1024) return null;

    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid('img_', true) . '.' . $ext;
    $dest     = __DIR__ . '/../uploads/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) return null;

    $data = [
        'filename'      => $filename,
        'original_name' => $file['name'],
        'alt_text'      => '',
        'mime_type'     => $mime,
        'size'          => $file['size'],
        'user_id'       => $userId,
    ];

    if (createMedia($data)) {
        return $data;
    }
    return null;
}