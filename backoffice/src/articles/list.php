<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$pageTitle   = 'Articles — Back-office';
$categories  = getCategories();
$users       = getUsers();

// Récupération des filtres
$filters = [
    'search'       => trim($_GET['search'] ?? ''),
    'categorie_id' => $_GET['categorie_id'] ?? '',
    'statut'       => $_GET['statut'] ?? '',
    'user_id'      => $_GET['user_id'] ?? '',
    'date_from'    => $_GET['date_from'] ?? '',
    'date_to'      => $_GET['date_to'] ?? '',
];

// Vérifie si des filtres sont actifs
$hasFilters = !empty($filters['search']) || !empty($filters['categorie_id']) ||
              !empty($filters['statut']) || !empty($filters['user_id']) ||
              !empty($filters['date_from']) || !empty($filters['date_to']);

// Récupère les articles (filtrés ou tous)
$articles = $hasFilters ? getArticlesFiltered($filters) : getArticles();

require '../includes/nav.php';
?>

<div class="page-header">
    <h1>Articles <span class="text-secondary fw-normal fs-6">(<?= count($articles) ?>)</span></h1>
    <a href="/articles/create.php" class="btn btn-dark btn-sm">+ Nouvel article</a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= ['created'=>'Article créé.','updated'=>'Article mis à jour.','deleted'=>'Article supprimé.'][$_GET['success']] ?? 'OK' ?></div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger">Une erreur est survenue.</div>
<?php endif; ?>

<!-- Filtres -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <!-- Recherche -->
            <div class="col-md-3">
                <label class="form-label" for="search">Rechercher</label>
                <input type="text" id="search" name="search" class="form-control"
                       placeholder="Mot-clé..." value="<?= htmlspecialchars($filters['search']) ?>">
            </div>

            <!-- Catégorie -->
            <div class="col-md-2">
                <label class="form-label" for="categorie_id">Catégorie</label>
                <select id="categorie_id" name="categorie_id" class="form-select">
                    <option value="">Toutes</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $filters['categorie_id'] == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Statut -->
            <div class="col-md-2">
                <label class="form-label" for="statut">Statut</label>
                <select id="statut" name="statut" class="form-select">
                    <option value="">Tous</option>
                    <option value="publie" <?= $filters['statut'] === 'publie' ? 'selected' : '' ?>>Publié</option>
                    <option value="brouillon" <?= $filters['statut'] === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                </select>
            </div>

            <!-- Auteur -->
            <div class="col-md-2">
                <label class="form-label" for="user_id">Auteur</label>
                <select id="user_id" name="user_id" class="form-select">
                    <option value="">Tous</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $filters['user_id'] == $u['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['email']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Date depuis -->
            <div class="col-md-2">
                <label class="form-label" for="date_from">Du</label>
                <input type="date" id="date_from" name="date_from" class="form-control"
                       value="<?= htmlspecialchars($filters['date_from']) ?>">
            </div>

            <!-- Date jusqu'à -->
            <div class="col-md-2">
                <label class="form-label" for="date_to">Au</label>
                <input type="date" id="date_to" name="date_to" class="form-control"
                       value="<?= htmlspecialchars($filters['date_to']) ?>">
            </div>

            <!-- Boutons -->
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark">Filtrer</button>
                <?php if ($hasFilters): ?>
                    <a href="/articles/list.php" class="btn btn-outline-secondary">Réinitialiser</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Liste des articles -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:35%">Titre</th>
                    <th>Catégorie</th>
                    <th>Auteur</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Image</th>
                    <th style="width:120px">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($articles)): ?>
                <tr><td colspan="7" class="text-center text-secondary py-4">
                    <?php if ($hasFilters): ?>
                        Aucun article ne correspond aux filtres. <a href="/articles/list.php">Réinitialiser</a>
                    <?php else: ?>
                        Aucun article. <a href="/articles/create.php">Créer le premier</a>
                    <?php endif; ?>
                </td></tr>
            <?php else: ?>
                <?php foreach ($articles as $a): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($a['titre']) ?></strong>
                        <div class="text-secondary" style="font-size:11px">/article/<?= $a['slug'] ?></div>
                    </td>
                    <td><?= htmlspecialchars($a['categorie'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($a['auteur'] ?? '—') ?></td>
                    <td><span class="badge-<?= $a['statut'] ?>"><?= $a['statut'] === 'publie' ? 'Publié' : 'Brouillon' ?></span></td>
                    <td><?= date('d/m/Y', strtotime($a['date_publication'])) ?></td>
                    <td>
                        <?php if ($a['image']): ?>
                            <img src="/uploads/<?= htmlspecialchars($a['image']) ?>"
                                 alt="<?= htmlspecialchars($a['alt_image']) ?>"
                                 style="width:48px;height:36px;object-fit:cover;border-radius:4px">
                        <?php else: ?>—<?php endif; ?>
                    </td>
                    <td>
                        <a href="/articles/edit.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-secondary me-1">Éditer</a>
                        <a href="/articles/delete.php?id=<?= $a['id'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Supprimer cet article ?')">Suppr.</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
