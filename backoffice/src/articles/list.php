<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
$pageTitle = 'Articles — Back-office';
$articles  = getArticles();
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
                    Aucun article. <a href="/articles/create.php">Créer le premier</a>
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