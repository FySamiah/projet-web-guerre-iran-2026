<?php
// frontoffice/src/pages/categorie.php
// Page catégorie - Articles d'une catégorie

$slug = $_GET['slug'] ?? null;

if (!$slug) {
    header('Location: /');
    exit;
}

$category = getCategoryBySlug($slug);

if (!$category) {
    http_response_code(404);
    include 'pages/404.php';
    exit;
}

// Articles de la catégorie
$articles = getArticlesByCategory($category['id'], 20);

// SEO
$pageTitle = 'Rubrique : ' . htmlspecialchars($category['nom']) . ' — IranInfo';
$metaDescription = htmlspecialchars($category['description'] ?? '') ?: 'Articles de la rubrique ' . htmlspecialchars($category['nom']);
$metaKeywords = htmlspecialchars($category['nom']) . ', iran, 2026';
$ogType = 'website';
$canonical = 'https://example.com/categorie/' . htmlspecialchars($slug);

include 'includes/header.php';
?>

<section class="categorie-page">
    <!-- En-tête de catégorie -->
    <div class="categorie-header">
        <h1 class="categorie-title">
            <?= htmlspecialchars($category['nom']) ?>
        </h1>
        <?php if ($category['description']): ?>
            <p class="categorie-description">
                <?= htmlspecialchars($category['description']) ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- Grille des articles -->
    <div class="categorie-content">
        <?php if (!empty($articles)): ?>
            <div class="grid" style="margin:0">
                <?php foreach ($articles as $article): ?>
                    <article class="art-card" style="border:1px solid #f0f0f0">
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
                                <?= htmlspecialchars($category['nom']) ?>
                            </div>
                            <h2 class="art-title">
                                <a href="/article/<?= htmlspecialchars($article['slug']) ?>">
                                    <?= htmlspecialchars($article['titre']) ?>
                                </a>
                            </h2>
                            <div class="art-date"><?= formatDate($article['date_publication']) ?></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="background:#fff;padding:20px;border-radius:4px;text-align:center;color:#999">
                <p>Aucun article dans cette rubrique pour le moment.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
