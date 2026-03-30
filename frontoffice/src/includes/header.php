<?php
// frontoffice/src/includes/header.php
// En-tête HTML, navigation, balises SEO premium

// Variables SEO (à définir dans chaque page)
$pageTitle       = $pageTitle ?? 'IranInfo — Actualités de la Guerre en Iran 2026';
$metaDescription = $metaDescription ?? 'Site d\'informations indépendant sur la situation en Iran 2026. Analyses politiques, militaires et humanitaires.';
$metaKeywords    = $metaKeywords ?? 'iran, guerre, 2026, militaire, politique, humanitaire';
$ogImage         = $ogImage ?? 'https://example.com/assets/images/og-image.jpg';
$ogType          = $ogType ?? 'website';
$canonical       = $canonical ?? 'https://example.com';
$author          = $author ?? 'La rédaction';

$categories = getCategories();
$currentDate = date('l d F Y', strtotime('now'));
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">

    <!-- SEO - TITLE ET DESCRIPTION -->
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($metaKeywords) ?>">
    <meta name="author" content="<?= htmlspecialchars($author) ?>">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="language" content="French">
    <meta name="revisit-after" content="7 days">
    
    <!-- OPEN GRAPH - RÉSEAUX SOCIAUX -->
    <meta property="og:type" content="<?= htmlspecialchars($ogType) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">
    <meta property="og:site_name" content="IranInfo">
    <meta property="og:locale" content="fr_FR">

    <!-- TWITTER CARD -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($ogImage) ?>">
    <meta name="twitter:site" content="@iraninfo">

    <!-- CANONICAL & ALTERNATE -->
    <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">
    <link rel="alternate" hreflang="fr" href="<?= htmlspecialchars($canonical) ?>">

    <!-- FAVICON & APP ICONS -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <meta name="theme-color" content="#1a1a2e">

    <!-- STYLESHEETS -->
    <link rel="stylesheet" href="/assets/css/style.css" media="screen">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://example.com">

    <!-- SCHEMA.ORG - ORGANISATION -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "NewsMediaOrganization",
        "name": "IranInfo",
        "url": "https://example.com",
        "logo": "https://example.com/logo.png",
        "sameAs": [
            "https://twitter.com/iraninfo",
            "https://facebook.com/iraninfo"
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "Editorial Contact",
            "email": "contact@example.com"
        }
    }
    </script>

</head>
<body>

<!-- EN-TÊTE DU SITE -->
<header class="site-header" role="banner">
    <div class="header-top">
        <div class="site-logo">
            <a href="/" title="Retour à l'accueil">IRAN<span>INFO</span></a>
        </div>
        <time class="header-date" datetime="<?= date('Y-m-d') ?>">
            <?php
                $jours = ['Sunday' => 'Dimanche', 'Monday' => 'Lundi', 'Tuesday' => 'Mardi', 
                         'Wednesday' => 'Mercredi', 'Thursday' => 'Jeudi', 'Friday' => 'Vendredi', 'Saturday' => 'Samedi'];
                $mois = ['January' => 'janvier', 'February' => 'février', 'March' => 'mars', 'April' => 'avril',
                        'May' => 'mai', 'June' => 'juin', 'July' => 'juillet', 'August' => 'août',
                        'September' => 'septembre', 'October' => 'octobre', 'November' => 'novembre', 'December' => 'décembre'];
                $jour = $jours[date('l')] ?? date('l');
                $mois_n = $mois[date('F')] ?? date('F');
                echo ucfirst($jour) . ' ' . date('d') . ' ' . $mois_n . ' ' . date('Y');
            ?>
        </time>
    </div>

    <!-- NAVIGATION PRINCIPALE -->
    <nav class="nav-bar" role="navigation" aria-label="Navigation principale">
        <a href="/" class="<?= strpos($_SERVER['REQUEST_URI'], '/') === 0 && $_SERVER['REQUEST_URI'] === '/' ? 'act' : '' ?>" title="Retour à l'accueil">
            Accueil
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="/categorie/<?= htmlspecialchars($cat['slug']) ?>"
               class="<?= isset($_GET['page']) && $_GET['page'] === 'categorie' && isset($_GET['slug']) && $_GET['slug'] === $cat['slug'] ? 'act' : '' ?>"
               title="Articles : <?= htmlspecialchars($cat['nom']) ?>">
                <?= htmlspecialchars($cat['nom']) ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- BREAKING NEWS -->
    <div class="breaking" role="alert">
        <span class="breaking-label">DIRECT</span>
        <span>Actualités en continu — Rafraîchissez la page pour les dernières infos</span>
    </div>
</header>

<div class="wrap" role="main">
