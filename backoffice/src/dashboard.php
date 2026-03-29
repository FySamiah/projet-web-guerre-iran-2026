<?php
// backoffice/src/dashboard.php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$pageTitle = 'Tableau de bord — Back-office';
$stats     = countArticles();
$cats      = getCategories();
$articles  = array_slice(getArticles(), 0, 5);

require 'includes/nav.php';
?>

<div class="page-header">
    <h1>Tableau de bord</h1>
    <a href="/articles/create.php" class="btn btn-dark btn-sm">+ Nouvel article</a>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-number"><?= $stats['total'] ?></div>
            <div class="stat-label">Total articles</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-number text-success"><?= $stats['publie'] ?></div>
            <div class="stat-label">Publiés</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-number text-secondary"><?= $stats['brouillon'] ?></div>
            <div class="stat-label">Brouillons</div>
        </div>
    </div>
</div>

<!-- Derniers articles -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong style="font-size:14px">5 derniers articles</strong>
        <a href="/articles/list.php" class="btn btn-outline-secondary btn-sm">Voir tout</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Auteur</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($articles as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['titre']) ?></td>
                    <td><?= htmlspecialchars($a['categorie'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($a['auteur'] ?? '—') ?></td>
                    <td><span class="badge-<?= $a['statut'] ?>"><?= $a['statut'] === 'publie' ? 'Publié' : 'Brouillon' ?></span></td>
                    <td><?= date('d/m/Y', strtotime($a['date_publication'])) ?></td>
                    <td><a href="/articles/edit.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-secondary">Éditer</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Catégories -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong style="font-size:14px">Catégories (<?= count($cats) ?>)</strong>
        <a href="/categories/list.php" class="btn btn-outline-secondary btn-sm">Gérer</a>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($cats as $c): ?>
                <span class="badge bg-light text-dark border" style="font-size:12px;padding:5px 10px">
                    <?= htmlspecialchars($c['nom']) ?> (<?= $c['nb_articles'] ?>)
                </span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>