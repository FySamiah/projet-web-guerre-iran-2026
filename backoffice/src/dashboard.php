<?php
// backoffice/src/dashboard.php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$pageTitle = 'Tableau de bord — Back-office';
$stats     = countArticles();
$cats      = getCategories();
$articles  = array_slice(getArticles(), 0, 5);
$byMonth   = getArticlesByMonth();
$byCat     = getArticlesByCategory();
$nbUsers   = countUsers();
$nbMedias  = countMedias();

require 'includes/nav.php';
?>

<div class="page-header">
    <h1>Tableau de bord</h1>
    <a href="/articles/create.php" class="btn btn-dark btn-sm">+ Nouvel article</a>
</div>

<!-- Stats principales -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card-primary">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                    <path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8zm0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?= $stats['total'] ?></div>
                <div class="stat-label">Articles</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card-success">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?= $stats['publie'] ?></div>
                <div class="stat-label">Publies</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card-info">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?= $stats['planifie'] ?? 0 ?></div>
                <div class="stat-label">Planifies</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card-secondary">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?= $stats['brouillon'] ?></div>
                <div class="stat-label">Brouillons</div>
            </div>
        </div>
    </div>
</div>

<!-- Stats secondaires -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card-mini">
            <span class="stat-mini-label">Categories</span>
            <span class="stat-mini-number"><?= count($cats) ?></span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card-mini">
            <span class="stat-mini-label">Utilisateurs</span>
            <span class="stat-mini-number"><?= $nbUsers ?></span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card-mini">
            <span class="stat-mini-label">Medias</span>
            <span class="stat-mini-number"><?= $nbMedias ?></span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card-mini">
            <span class="stat-mini-label">Role</span>
            <span class="stat-mini-number"><?= ucfirst($currentUser['role']) ?></span>
        </div>
    </div>
</div>

<!-- Graphiques -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h2 class="h6 mb-0">Articles publies (6 derniers mois)</h2>
            </div>
            <div class="card-body">
                <canvas id="chartArticlesMois" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h2 class="h6 mb-0">Repartition par categorie</h2>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartCategories"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Derniers articles -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center border-0">
        <h2 class="h6 mb-0">Derniers articles</h2>
        <a href="/articles/list.php" class="btn btn-outline-dark btn-sm">Voir tout</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Categorie</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($articles)): ?>
                <tr><td colspan="5" class="text-center text-secondary py-4">Aucun article</td></tr>
            <?php else: ?>
                <?php foreach ($articles as $a): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($a['titre']) ?></strong></td>
                    <td><span class="badge bg-light text-dark"><?= htmlspecialchars($a['categorie'] ?? '—') ?></span></td>
                    <td>
                        <?php if ($a['statut'] === 'publie'): ?>
                            <span class="badge bg-success">Publie</span>
                        <?php elseif ($a['statut'] === 'planifie'): ?>
                            <span class="badge bg-info">Planifie</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Brouillon</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($a['date_publication'])) ?></td>
                    <td><a href="/articles/edit.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-dark">Editer</a></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Categories -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center border-0">
        <h2 class="h6 mb-0">Categories</h2>
        <a href="/categories/list.php" class="btn btn-outline-dark btn-sm">Gerer</a>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($cats as $c): ?>
                <span class="category-badge">
                    <?= htmlspecialchars($c['nom']) ?>
                    <span class="category-count"><?= $c['nb_articles'] ?></span>
                </span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// Donnees pour le graphique par mois
const moisLabels = <?= json_encode(array_map(function($m) {
    $date = DateTime::createFromFormat('Y-m', $m['mois']);
    return $date ? $date->format('M Y') : $m['mois'];
}, $byMonth)) ?>;
const moisData = <?= json_encode(array_map('intval', array_column($byMonth, 'nb'))) ?>;

// Graphique articles par mois
const ctx1 = document.getElementById('chartArticlesMois');
const gradient = ctx1.getContext('2d').createLinearGradient(0, 0, 0, 300);
gradient.addColorStop(0, '#667eea');
gradient.addColorStop(1, '#764ba2');

new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: moisLabels.length ? moisLabels : ['Aucune donnee'],
        datasets: [{
            label: 'Articles',
            data: moisData.length ? moisData : [0],
            backgroundColor: [
                '#FF6B6B',
                '#4ECDC4',
                '#45B7D1',
                '#96CEB4',
                '#FFEAA7',
                '#DDA0DD'
            ],
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// Donnees pour le graphique par categorie
const catLabels = <?= json_encode(array_column($byCat, 'nom')) ?>;
const catData = <?= json_encode(array_map('intval', array_column($byCat, 'nb'))) ?>;
const catColors = [
    '#FF6B6B',  // Rouge corail
    '#4ECDC4',  // Turquoise
    '#45B7D1',  // Bleu ciel
    '#96CEB4',  // Vert menthe
    '#FFEAA7',  // Jaune pastel
    '#DDA0DD',  // Rose plum
    '#FF8C42',  // Orange
    '#98D8C8'   // Vert eau
];

// Graphique categories
new Chart(document.getElementById('chartCategories'), {
    type: 'doughnut',
    data: {
        labels: catLabels,
        datasets: [{
            data: catData,
            backgroundColor: catColors.slice(0, catLabels.length),
            borderWidth: 3,
            borderColor: '#ffffff',
            hoverBorderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '60%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 14,
                    padding: 12,
                    font: { size: 12, weight: '500' },
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            }
        }
    }
});
</script>

<?php require 'includes/footer.php'; ?>
