<?php
// backoffice/src/includes/nav.php
$currentPage = basename($_SERVER['PHP_SELF']);
$currentUri  = $_SERVER['REQUEST_URI'];

// Variables SEO par défaut (peuvent être redéfinies dans chaque page)
$metaDescription = $metaDescription ?? 'Back-office de gestion du site d\'information sur la guerre en Iran 2026';
$metaKeywords    = $metaKeywords ?? 'back-office, administration, iran, guerre, 2026';
$metaAuthor      = $metaAuthor ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($metaKeywords) ?>">
    <meta name="author" content="<?= htmlspecialchars($metaAuthor) ?>">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($pageTitle ?? 'Back-office') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="d-flex" id="wrapper">

    <!-- ── Sidebar ── -->
    <nav class="sidebar bg-dark text-white d-flex flex-column p-3">
        <div class="mb-4">
            <div class="text-uppercase text-secondary" style="font-size:10px;letter-spacing:.1em;font-weight:600">
                Guerre en Iran
            </div>
            <span class="badge bg-secondary mt-1">Back-office</span>
        </div>

        <ul class="nav flex-column gap-1 flex-grow-1">
            <li>
                <a href="/dashboard.php"
                   class="nav-link text-white rounded <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                    Tableau de bord
                </a>
            </li>
            <li>
                <a href="/articles/list.php"
                   class="nav-link text-white rounded <?= str_contains($currentUri, '/articles/') ? 'active' : '' ?>">
                    Articles
                </a>
            </li>
            <li>
                <a href="/categories/list.php"
                   class="nav-link text-white rounded <?= str_contains($currentUri, '/categories/') ? 'active' : '' ?>">
                    Catégories
                </a>
            </li>
            <li>
                <a href="/media/list.php"
                   class="nav-link text-white rounded <?= str_contains($currentUri, '/media/') ? 'active' : '' ?>">
                    Mediatheque
                </a>
            </li>
            <?php if (isset($currentUser) && $currentUser['role'] === 'admin'): ?>
            <li>
                <a href="/users/list.php"
                   class="nav-link text-white rounded <?= str_contains($currentUri, '/users/') ? 'active' : '' ?>">
                    Utilisateurs
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <div class="pt-3 mt-auto border-top border-secondary">
            <div class="text-secondary mb-2" style="font-size:12px">
                <?= htmlspecialchars($currentUser['email']) ?>
            </div>
            <a href="/logout.php" class="btn btn-outline-danger btn-sm w-100">
                Déconnexion
            </a>
        </div>
    </nav>

    <!-- ── Contenu ── -->
    <main class="flex-grow-1 p-4">