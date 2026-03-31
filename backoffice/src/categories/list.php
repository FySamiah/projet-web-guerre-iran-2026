<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
$pageTitle  = 'Catégories — Back-office';
$categories = getCategories();
require '../includes/nav.php';
?>

<div class="page-header">
    <h1>Catégories</h1>
    <a href="/admin/categories/create.php" class="btn btn-dark btn-sm">+ Nouvelle catégorie</a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= ['created'=>'Catégorie créée.','updated'=>'Catégorie mise à jour.','deleted'=>'Catégorie supprimée.'][$_GET['success']] ?? 'OK' ?></div>
<?php endif; ?>
<?php if (isset($_GET['error']) && $_GET['error'] === 'has_articles'): ?>
    <div class="alert alert-danger">Impossible : des articles sont liés à cette catégorie.</div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Slug</th>
                    <th>Articles</th>
                    <th>Description</th>
                    <th style="width:120px">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($categories)): ?>
                <tr><td colspan="5" class="text-center text-secondary py-4">
                    Aucune catégorie. <a href="/admin/categories/create.php">Créer la première</a>
                </td></tr>
            <?php else: ?>
                <?php foreach ($categories as $c): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($c['nom']) ?></strong></td>
                    <td><code style="font-size:12px">/categorie/<?= htmlspecialchars($c['slug']) ?></code></td>
                    <td><span class="badge bg-light text-dark border"><?= $c['nb_articles'] ?></span></td>
                    <td class="text-secondary" style="font-size:12px"><?= htmlspecialchars(mb_substr($c['description'] ?? '', 0, 55)) ?></td>
                    <td>
                        <a href="/admin/categories/edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-secondary me-1">Éditer</a>
                        <?php if ($c['nb_articles'] == 0): ?>
                            <a href="/admin/categories/delete.php?id=<?= $c['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Supprimer cette catégorie ?')">Suppr.</a>
                        <?php else: ?>
                            <button class="btn btn-sm btn-outline-danger" disabled title="Articles liés">Suppr.</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../includes/footer.php'; ?>