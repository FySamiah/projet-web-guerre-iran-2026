<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$id      = (int)($_GET['id'] ?? 0);
$article = getArticleById($id);
if (!$article) { header('Location: /articles/list.php?error=notfound'); exit; }

$pageTitle  = 'Éditer — ' . $article['titre'];
$categories = getCategories();
$errors     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre        = trim($_POST['titre']        ?? '');
    $contenu      = trim($_POST['contenu']      ?? '');
    $resume       = trim($_POST['resume']       ?? '');
    $alt_image    = trim($_POST['alt_image']    ?? '');
    $categorie_id = (int)($_POST['categorie_id'] ?? 0);
    $statut       = in_array($_POST['statut'] ?? '', ['publie','brouillon']) ? $_POST['statut'] : 'brouillon';

    if ($titre === '')       $errors[] = 'Le titre est obligatoire.';
    if ($contenu === '')     $errors[] = 'Le contenu est obligatoire.';
    if ($alt_image === '')   $errors[] = 'La description alt est obligatoire.';
    if ($categorie_id === 0) $errors[] = 'Choisissez une catégorie.';

    $imageFilename = $article['image'];
    if (!empty($_FILES['image']['name'])) {
        $new = uploadImage($_FILES['image']);
        if ($new === null) {
            $errors[] = 'Image invalide (JPG/PNG/WebP, max 5 Mo).';
        } else {
            // Supprimer ancienne image
            if ($article['image']) {
                $old = __DIR__ . '/../uploads/' . $article['image'];
                if (file_exists($old)) unlink($old);
            }
            $imageFilename = $new;
        }
    }

    if (empty($errors)) {
        $ok = updateArticle($id, [
            'titre'        => $titre,
            'slug'         => slugify($titre),
            'contenu'      => $contenu,
            'resume'       => $resume,
            'image'        => $imageFilename,
            'alt_image'    => $alt_image,
            'categorie_id' => $categorie_id,
            'statut'       => $statut,
        ]);
        if ($ok) { header('Location: /articles/list.php?success=updated'); exit; }
        $errors[] = 'Erreur lors de la mise à jour.';
    }

    $article = array_merge($article, compact('titre','contenu','resume','alt_image','categorie_id','statut'));
}

require '../includes/nav.php';
?>

<div class="page-header">
    <h1>Éditer l'article</h1>
    <a href="/articles/list.php" class="btn btn-outline-secondary btn-sm">← Retour</a>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger"><ul class="mb-0 ps-3"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label" for="titre">Titre <span class="text-danger">*</span></label>
                <input type="text" id="titre" name="titre" class="form-control" required
                       value="<?= htmlspecialchars($article['titre']) ?>">
                <div class="form-text text-secondary">Slug actuel : /article/<?= htmlspecialchars($article['slug']) ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="resume">Résumé</label>
                <textarea id="resume" name="resume" class="form-control" rows="2" maxlength="155"><?= htmlspecialchars($article['resume'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label" for="contenu">Contenu <span class="text-danger">*</span></label>
                <textarea id="contenu" name="contenu" class="form-control" rows="12" required><?= htmlspecialchars($article['contenu'] ?? '') ?></textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-5">
                    <label class="form-label" for="categorie_id">Catégorie</label>
                    <select id="categorie_id" name="categorie_id" class="form-select">
                        <option value="">— Choisir —</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($article['categorie_id'] == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="statut">Statut</label>
                    <select id="statut" name="statut" class="form-select">
                        <option value="brouillon" <?= ($article['statut'] === 'brouillon') ? 'selected' : '' ?>>Brouillon</option>
                        <option value="publie"    <?= ($article['statut'] === 'publie')    ? 'selected' : '' ?>>Publié</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-5">
                    <label class="form-label" for="image">
                        Nouvelle image
                        <span class="text-secondary fw-normal">(laisser vide = conserver)</span>
                    </label>
                    <?php if ($article['image']): ?>
                        <div class="mb-2">
                            <img src="/uploads/<?= htmlspecialchars($article['image']) ?>"
                                 alt="<?= htmlspecialchars($article['alt_image']) ?>"
                                 style="max-width:200px;max-height:120px;border-radius:6px;border:1px solid #e3e6ef">
                            <div class="form-text">Image actuelle</div>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="image" name="image" class="form-control"
                           accept="image/jpeg,image/png,image/webp" onchange="previewImage(this)">
                    <img id="image-preview" src="" alt="Aperçu">
                </div>
                <div class="col-md-7">
                    <label class="form-label" for="alt_image">Description image (alt) <span class="text-danger">*</span></label>
                    <input type="text" id="alt_image" name="alt_image" class="form-control" required
                           value="<?= htmlspecialchars($article['alt_image'] ?? '') ?>">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark">Enregistrer les modifications</button>
                <a href="/articles/list.php" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const p = document.getElementById('image-preview');
    if (input.files && input.files[0]) {
        const r = new FileReader();
        r.onload = e => { p.src = e.target.result; p.style.display = 'block'; };
        r.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require '../includes/footer.php'; ?>