<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$pageTitle  = 'Nouvel article — Back-office';
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
    if ($alt_image === '')   $errors[] = 'La description alt de l\'image est obligatoire.';
    if ($categorie_id === 0) $errors[] = 'Choisissez une catégorie.';

    $imageFilename = null;
    if (!empty($_FILES['image']['name'])) {
        $imageFilename = uploadImage($_FILES['image']);
        if ($imageFilename === null)
            $errors[] = 'Image invalide (JPG/PNG/WebP, max 5 Mo).';
    }

    if (empty($errors)) {
        $ok = createArticle([
            'titre'        => $titre,
            'slug'         => slugify($titre),
            'contenu'      => $contenu,
            'resume'       => $resume,
            'image'        => $imageFilename,
            'alt_image'    => $alt_image,
            'categorie_id' => $categorie_id,
            'user_id'      => $currentUser['id'],
            'statut'       => $statut,
        ]);
        if ($ok) { header('Location: /articles/list.php?success=created'); exit; }
        $errors[] = 'Erreur enregistrement (titre déjà utilisé ?).';
    }
}

require '../includes/nav.php';
?>

<div class="page-header">
    <h1>Nouvel article</h1>
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
                <input type="text" id="titre" name="titre" class="form-control"
                       value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>"
                       placeholder="Ex : Offensive militaire au nord de l'Iran" required autofocus>
                <div class="form-text text-secondary">Le slug URL sera généré automatiquement.</div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="resume">Résumé <span class="text-secondary fw-normal">(meta description, 155 car. max)</span></label>
                <textarea id="resume" name="resume" class="form-control" rows="2" maxlength="155"
                          placeholder="Résumé affiché sur l'accueil et dans Google"><?= htmlspecialchars($_POST['resume'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label" for="contenu">Contenu <span class="text-danger">*</span></label>
                <textarea id="contenu" name="contenu" class="form-control" rows="12" required
                          placeholder="Rédigez votre article... (HTML autorisé : h2, h3, p, strong, ul...)"><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-5">
                    <label class="form-label" for="categorie_id">Catégorie <span class="text-danger">*</span></label>
                    <select id="categorie_id" name="categorie_id" class="form-select" required>
                        <option value="">— Choisir —</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (($_POST['categorie_id'] ?? '') == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="statut">Statut</label>
                    <select id="statut" name="statut" class="form-select">
                        <option value="brouillon" <?= (($_POST['statut'] ?? 'brouillon') === 'brouillon') ? 'selected' : '' ?>>Brouillon</option>
                        <option value="publie"    <?= (($_POST['statut'] ?? '') === 'publie') ? 'selected' : '' ?>>Publié</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-5">
                    <label class="form-label" for="image">Image</label>
                    <input type="file" id="image" name="image" class="form-control"
                           accept="image/jpeg,image/png,image/webp"
                           onchange="previewImage(this)">
                    <img id="image-preview" src="" alt="Aperçu">
                </div>
                <div class="col-md-7">
                    <!-- CHAMP ALT — point 5 de l'énoncé -->
                    <label class="form-label" for="alt_image">
                        Description image (alt) <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="alt_image" name="alt_image" class="form-control" required
                           value="<?= htmlspecialchars($_POST['alt_image'] ?? '') ?>"
                           placeholder="Ex : Soldats iraniens lors de l'offensive du 15 mars">
                    <div class="form-text text-secondary">Obligatoire pour le SEO et l'accessibilité.</div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark">Enregistrer</button>
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