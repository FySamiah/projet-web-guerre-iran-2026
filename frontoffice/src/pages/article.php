<?php
// frontoffice/src/pages/article.php
// Page article unique

$slug = $_GET['slug'] ?? null;

if (!$slug) {
    header('Location: /');
    exit;
}

$article = getArticleBySlug($slug);

if (!$article) {
    http_response_code(404);
    include 'pages/404.php';
    exit;
}

// SEO
$pageTitle = htmlspecialchars($article['titre']) . ' — IranInfo';
$metaDescription = htmlspecialchars($article['resume'] ?? '');
$metaKeywords = htmlspecialchars($article['categorie'] ?? '');
$ogImage = $article['image'] ? 'https://example.com/uploads/' . htmlspecialchars($article['image']) : '/assets/images/og-image.jpg';
$ogType = 'article';
$canonical = 'https://example.com/article/' . htmlspecialchars($slug);

include 'includes/header.php';
?>

<article class="article-page" itemscope itemtype="https://schema.org/NewsArticle">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <a href="/">Accueil</a>
        <span class="breadcrumb-sep">›</span>
        <?php if ($article['categorie']): ?>
            <a href="/categorie/<?= htmlspecialchars(slugify($article['categorie'])) ?>">
                <?= htmlspecialchars($article['categorie']) ?>
            </a>
            <span class="breadcrumb-sep">›</span>
        <?php endif; ?>
        <span><?= htmlspecialchars(truncate($article['titre'], 50)) ?></span>
    </nav>

    <div style="background:#fff;border-radius:4px;padding:16px;margin-top:12px">
        <!-- Catégorie -->
        <span class="article-category">
            <?= htmlspecialchars($article['categorie'] ?? 'Non catégorisé') ?>
        </span>

        <!-- H1 Titre -->
        <h1 class="article-title" itemprop="headline">
            <?= htmlspecialchars($article['titre']) ?>
        </h1>

        <!-- Métadonnées -->
        <div class="article-meta">
            <span itemprop="author"><?= htmlspecialchars($article['auteur'] ?? 'La rédaction') ?></span>
            <span class="article-date" itemprop="datePublished">
                <?= formatDate($article['date_publication'], 'd F Y \à H\hi') ?>
            </span>
            <span class="article-reading-time">5 min de lecture</span>
        </div>

        <!-- Image d'illustration -->
        <?php if ($article['image']): ?>
            <figure class="article-figure" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                <img src="/uploads/<?= htmlspecialchars($article['image']) ?>"
                     alt="<?= htmlspecialchars($article['alt_image'] ?? $article['titre']) ?>"
                     class="article-img"
                     itemprop="url"
                     loading="lazy">
                <figcaption itemprop="caption">
                    <?= htmlspecialchars($article['alt_image'] ?? $article['titre']) ?>
                </figcaption>
            </figure>
        <?php endif; ?>

        <!-- Résumé lead -->
        <p class="article-lead" itemprop="description">
            <?= htmlspecialchars($article['resume']) ?>
        </p>

        <!-- Contenu article -->
        <div class="article-content" itemprop="articleBody">
            <?= html_entity_decode($article['contenu']) ?>
        </div>

        <!-- Articles connexes -->
        <aside class="article-related">
            <strong>Articles liés</strong>
            <?php
                $relatedArticles = getArticlesByCategory($article['categorie_id'], 3);
                foreach ($relatedArticles as $related):
                    if ($related['slug'] === $slug) continue;
            ?>
                <a href="/article/<?= htmlspecialchars($related['slug']) ?>">
                    → <?= htmlspecialchars($related['titre']) ?>
                </a>
            <?php endforeach; ?>
        </aside>
    </div>

</article>

<!-- Structured Data (Schema.org) -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "headline": "<?= addslashes($article['titre']) ?>",
    "description": "<?= addslashes($article['resume']) ?>",
    "image": "https://example.com/uploads/<?= $article['image'] ?>",
    "datePublished": "<?= $article['date_publication'] ?>",
    "author": {
        "@type": "Person",
        "name": "<?= addslashes($article['auteur'] ?? 'La rédaction') ?>"
    },
    "publisher": {
        "@type": "Organization",
        "name": "IranInfo",
        "logo": {
            "@type": "ImageObject",
            "url": "https://example.com/favicon.ico"
        }
    }
}
</script>

<?php include 'includes/footer.php'; ?>
