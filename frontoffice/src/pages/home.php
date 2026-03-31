<?php
// frontoffice/src/pages/home.php
// Page d'accueil - Articles récents

$pageTitle = 'IranInfo — Actualités de la Guerre en Iran';
$metaDescription = 'Suivez les dernières actualités sur la situation en Iran 2026. Analyses politiques, militaires et humanitaires.';
$metaKeywords = 'iran, guerre, 2026, actualités, informations';
$ogType = 'website';
$canonical = 'https://example.com/';

// Récupère les articles pour l'affichage
$articles = getArticles();
$recentArticles = getRecentArticles(4);

// Inclut l'en-tête
include 'includes/header.php';
?>

<?php if (!empty($articles)): ?>
    <!-- Hero article à la une -->
    <article class="hero">
        <?php if ($articles[0]['image']): ?>
            <img src="/uploads/<?= htmlspecialchars($articles[0]['image']) ?>" 
                 alt="<?= htmlspecialchars($articles[0]['alt_image'] ?? $articles[0]['titre']) ?>"
                 class="hero-img">
        <?php else: ?>
            <div class="hero-img"></div>
        <?php endif; ?>
        <div class="hero-content">
            <span class="hero-badge">À la une</span>
            <h1 class="hero-title">
                <a href="/article/<?= htmlspecialchars($articles[0]['slug']) ?>">
                    <?= htmlspecialchars($articles[0]['titre']) ?>
                </a>
            </h1>
            <p class="hero-resume">
                <?= htmlspecialchars(truncate($articles[0]['resume'], 200)) ?>
            </p>
            <div class="hero-meta">
                <span class="cat">
                    <?php
                        $cat = getCategoryById($articles[0]['categorie_id']);
                        echo $cat ? htmlspecialchars($cat['nom']) : 'Non catégorisé';
                    ?>
                </span>
                <span><?= formatDate($articles[0]['date_publication']) ?></span>
                <span>5 min de lecture</span>
            </div>
        </div>
    </article>
<?php endif; ?>

<!-- Grille des articles récents (2 colonnes) -->
<h2 class="section-title"><span>Dernières nouvelles</span></h2>
<div class="grid">
    <?php 
        // Affiche les 4 articles suivants
        for ($i = 1; $i < min(5, count($articles)); $i++):
            $article = $articles[$i];
            $category = getCategoryById($article['categorie_id']);
    ?>
        <article class="art-card">
            <div class="art-card-img">
                <?php if ($article['image']): ?>
                    <img src="/uploads/<?= htmlspecialchars($article['image']) ?>" 
                         alt="<?= htmlspecialchars($article['alt_image'] ?? $article['titre']) ?>"
                         loading="lazy">
                <?php else: ?>
                    <div style="width:100%;height:100%;background:#ddd;display:flex;align-items:center;justify-content:center;color:#999">
                        Pas d'image
                    </div>
                <?php endif; ?>
            </div>
            <div class="art-card-body">
                <div class="art-cat">
                    <?= $category ? htmlspecialchars($category['nom']) : 'Non catégorisé' ?>
                </div>
                <h2 class="art-title">
                    <a href="/article/<?= htmlspecialchars($article['slug']) ?>">
                        <?= htmlspecialchars($article['titre']) ?>
                    </a>
                </h2>
                <div class="art-date"><?= formatDate($article['date_publication']) ?></div>
            </div>
        </article>
    <?php endfor; ?>
</div>

<!-- Articles populaires (liste) -->
<h2 class="section-title"><span>Les plus lus</span></h2>
<div class="list-section">
    <?php 
        $count = min(3, count($recentArticles));
        for ($i = 0; $i < $count; $i++):
            $article = $recentArticles[$i];
            $category = getCategoryById($article['categorie_id']);
    ?>
        <article class="list-item">
            <div class="list-num"><?= $i + 1 ?></div>
            <div class="list-content">
                <div class="list-cat">
                    <?= $category ? htmlspecialchars($category['nom']) : 'Non catégorisé' ?>
                </div>
                <h2 class="list-title">
                    <a href="/article/<?= htmlspecialchars($article['slug']) ?>">
                        <?= htmlspecialchars($article['titre']) ?>
                    </a>
                </h2>
            </div>
        </article>
    <?php endfor; ?>
</div>

<?php include 'includes/footer.php'; ?>
