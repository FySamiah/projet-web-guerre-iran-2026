<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Seuls les admins peuvent gérer les utilisateurs
if ($currentUser['role'] !== 'admin') {
    header('Location: /admin/dashboard.php');
    exit;
}

$pageTitle = 'Utilisateurs — Back-office';
$users     = getUsers();

require '../includes/nav.php';
?>

<div class="page-header">
    <h1>Utilisateurs <span class="text-secondary fw-normal fs-6">(<?= count($users) ?>)</span></h1>
    <a href="/admin/users/create.php" class="btn btn-dark btn-sm">+ Nouvel utilisateur</a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <?= ['created'=>'Utilisateur créé.','updated'=>'Utilisateur mis à jour.','deleted'=>'Utilisateur supprimé.'][$_GET['success']] ?? 'OK' ?>
    </div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?= $_GET['error'] === 'last_admin' ? 'Impossible de supprimer le dernier administrateur.' : 'Une erreur est survenue.' ?>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Inscrit le</th>
                    <th style="width:120px">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="6" class="text-center text-secondary py-4">Aucun utilisateur.</td></tr>
            <?php else: ?>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u['nom'] ?: '—') ?></strong></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : ($u['role'] === 'editeur' ? 'warning' : 'secondary') ?>">
                            <?= ucfirst($u['role']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-<?= $u['actif'] ? 'success' : 'secondary' ?>">
                            <?= $u['actif'] ? 'Actif' : 'Inactif' ?>
                        </span>
                    </td>
                    <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <a href="/admin/users/edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-secondary me-1">Éditer</a>
                        <?php if ($u['id'] !== $currentUser['id']): ?>
                            <a href="/admin/users/delete.php?id=<?= $u['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Supprimer cet utilisateur ?')">Suppr.</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white">
        <strong style="font-size:14px">Rôles et permissions</strong>
    </div>
    <div class="card-body">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Rôle</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge bg-danger">Admin</span></td>
                    <td>Accès complet : articles, catégories, médias, utilisateurs</td>
                </tr>
                <tr>
                    <td><span class="badge bg-warning">Éditeur</span></td>
                    <td>Gestion des articles et catégories, médias</td>
                </tr>
                <tr>
                    <td><span class="badge bg-secondary">Rédacteur</span></td>
                    <td>Création et modification de ses propres articles</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
