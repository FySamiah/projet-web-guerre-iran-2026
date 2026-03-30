<?php
// backoffice/src/categories/edit.php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$id       = (int)($_GET['id'] ?? 0);
$categorie = getCategorieById($id);

if (!$categorie) {
    header('Location: /admin/categories/list.php?error=notfound');
    exit;
}

$pageTitle = 'Éditer — ' . $categorie['nom'];
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom         = trim($_POST['nom']         ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($nom === '') $errors[] = 'Le nom est obligatoire.';

    if (empty($errors)) {
        $ok = updateCategorie($id, [
            'nom'         => $nom,
            'slug'        => slugify($nom),
            'description' => $description,
        ]);

        if ($ok) {
            header('Location: /admin/categories/list.php?success=updated');
            exit;
        }
        $errors[] = 'Erreur lors de la mise à jour.';
    }

    $categorie['nom']         = $nom;
    $categorie['description'] = $description;
}

require '../includes/nav.php';
?>

<div class="page-header">
    <h1>Éditer la catégorie</h1>
    <a href="/admin/categories/list.php" class="btn btn-outline-secondary btn-sm">← Retour</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="max-width:500px">
    <div class="card-body">
        <form method="POST">

            <div class="mb-3">
                <label class="form-label" for="nom">
                    Nom <span class="text-danger">*</span>
                </label>
                <input
                    type="text" id="nom" name="nom"
                    class="form-control"
                    value="<?= htmlspecialchars($categorie['nom']) ?>"
                    required
                >
                <div class="form-text text-secondary">
                    Slug actuel : /categorie/<?= htmlspecialchars($categorie['slug']) ?>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label" for="description">Description</label>
                <textarea
                    id="description" name="description"
                    class="form-control" rows="3"
                ><?= htmlspecialchars($categorie['description'] ?? '') ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark">
                    Enregistrer
                </button>
                <a href="/admin/categories/list.php" class="btn btn-outline-secondary">
                    Annuler
                </a>
            </div>

        </form>
    </div>
</div>

<?php require '../includes/footer.php'; ?>