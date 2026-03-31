<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$pageTitle  = 'Nouvel article — Back-office';
$categories = getCategories();
$errors     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre            = trim($_POST['titre'] ?? '');
    $contenu          = trim($_POST['contenu'] ?? '');
    $resume           = trim($_POST['resume'] ?? '');
    $alt_image        = trim($_POST['alt_image'] ?? '');
    $categorie_id     = (int)($_POST['categorie_id'] ?? 0);
    $statut           = in_array($_POST['statut'] ?? '', ['publie','brouillon','planifie']) ? $_POST['statut'] : 'brouillon';
    $date_publication = $_POST['date_publication'] ?? '';

    if ($titre === '')       $errors[] = 'Le titre est obligatoire.';
    if ($contenu === '')     $errors[] = 'Le contenu est obligatoire.';
    if ($alt_image === '')   $errors[] = 'La description alt de l\'image est obligatoire.';
    if ($categorie_id === 0) $errors[] = 'Choisissez une catégorie.';

    if ($statut === 'planifie') {
        if (empty($date_publication)) {
            $errors[] = 'La date de publication est obligatoire pour un article planifié.';
        } elseif (strtotime($date_publication) <= time()) {
            $errors[] = 'La date de publication doit être dans le futur.';
        }
    }

    $imageFilename = null;
    if (!empty($_FILES['image']['name'])) {
        $imageFilename = uploadImage($_FILES['image']);
        if ($imageFilename === null)
            $errors[] = 'Image invalide (JPG/PNG/WebP, max 5 Mo).';
    }

    if (empty($errors)) {
        $ok = createArticle([
            'titre'            => $titre,
            'slug'             => slugify($titre),
            'contenu'          => $contenu,
            'resume'           => $resume,
            'image'            => $imageFilename,
            'alt_image'        => $alt_image,
            'categorie_id'     => $categorie_id,
            'user_id'          => $currentUser['id'],
            'statut'           => $statut,
            'date_publication' => $statut === 'planifie' ? $date_publication : date('Y-m-d H:i:s'),
        ]);
        if ($ok) { header('Location: /admin/articles/list.php?success=created'); exit; }
        $errors[] = 'Erreur enregistrement (titre déjà utilisé ?).';
    }
}

require '../includes/nav.php';
?>

<div class="page-header">
    <h1>Nouvel article</h1>
    <a href="/admin/articles/list.php" class="btn btn-outline-secondary btn-sm">← Retour</a>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" id="article-form">

            <!-- Titre -->
            <div class="mb-3">
                <label class="form-label" for="titre">
                    Titre <span class="text-danger">*</span>
                </label>
                <input type="text" id="titre" name="titre" class="form-control"
                       value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>"
                       placeholder="Ex : Offensive militaire au nord de l'Iran"
                       required autofocus>
                <div class="form-text text-secondary">Le slug URL sera généré automatiquement.</div>
            </div>

            <!-- Résumé -->
            <div class="mb-3">
                <label class="form-label" for="resume">
                    Résumé
                    <span class="text-secondary fw-normal">(meta description, 155 car. max)</span>
                </label>
                <textarea id="resume" name="resume" class="form-control" rows="2" maxlength="155"
                          placeholder="Résumé affiché sur l'accueil et dans Google"><?= htmlspecialchars($_POST['resume'] ?? '') ?></textarea>
            </div>

            <!-- Contenu — TinyMCE remplace ce textarea -->
            <div class="mb-3">
                <label class="form-label" for="contenu">
                    Contenu <span class="text-danger">*</span>
                </label>
                <textarea id="contenu" name="contenu"><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
                <div class="form-text text-secondary mt-1">
                    Structurez votre article avec Titre 2, Titre 3, listes, gras...
                    <strong class="text-danger">N'utilisez pas "Titre 1"</strong>
                    — le h1 est réservé au titre de l'article.
                </div>
            </div>

            <!-- Catégorie + Statut + Date -->
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label" for="categorie_id">
                        Catégorie <span class="text-danger">*</span>
                    </label>
                    <select id="categorie_id" name="categorie_id" class="form-select" required>
                        <option value="">— Choisir —</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"
                                <?= (($_POST['categorie_id'] ?? '') == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="statut">Statut</label>
                    <select id="statut" name="statut" class="form-select"
                            onchange="toggleDatePublication()">
                        <option value="brouillon"
                            <?= (($_POST['statut'] ?? 'brouillon') === 'brouillon') ? 'selected' : '' ?>>
                            Brouillon
                        </option>
                        <option value="publie"
                            <?= (($_POST['statut'] ?? '') === 'publie') ? 'selected' : '' ?>>
                            Publié
                        </option>
                        <option value="planifie"
                            <?= (($_POST['statut'] ?? '') === 'planifie') ? 'selected' : '' ?>>
                            Planifié
                        </option>
                    </select>
                </div>
                <div class="col-md-4" id="date-publication-container"
                     style="<?= (($_POST['statut'] ?? '') !== 'planifie') ? 'display:none' : '' ?>">
                    <label class="form-label" for="date_publication">Date de publication</label>
                    <input type="datetime-local" id="date_publication"
                           name="date_publication" class="form-control"
                           value="<?= htmlspecialchars($_POST['date_publication'] ?? '') ?>"
                           min="<?= date('Y-m-d\TH:i') ?>">
                    <div class="form-text text-secondary">L'article sera publié automatiquement.</div>
                </div>
            </div>

            <!-- Image + ALT -->
            <div class="row g-3 mb-4">
                <div class="col-md-5">
                    <label class="form-label" for="image">Image</label>
                    <input type="file" id="image" name="image" class="form-control"
                           accept="image/jpeg,image/png,image/webp"
                           onchange="previewImage(this)">
                    <img id="image-preview" src="" alt="Aperçu"
                         style="display:none;max-width:200px;margin-top:10px;
                                border-radius:6px;border:1px solid #e3e6ef">
                </div>
                <div class="col-md-7">
                    <!-- ALT obligatoire — point 5 de l'énoncé -->
                    <label class="form-label" for="alt_image">
                        Description image (alt) <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="alt_image" name="alt_image"
                           class="form-control" required
                           value="<?= htmlspecialchars($_POST['alt_image'] ?? '') ?>"
                           placeholder="Ex : Soldats iraniens lors de l'offensive du 15 mars">
                    <div class="form-text text-secondary">
                        Obligatoire pour le SEO et l'accessibilité.
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark">Enregistrer</button>
                <a href="/admin/articles/list.php" class="btn btn-outline-secondary">Annuler</a>
            </div>

        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════
     TinyMCE 6 — CDN jsDelivr (sans clé API)
     ═══════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
<script>
tinymce.init({
    selector: '#contenu',

    // Langue française
    language: 'fr_FR',
    language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@23.10.9/langs6/fr_FR.js',

    // Hauteur
    height: 450,
    min_height: 300,
    resize: true,

    // Plugins
    plugins: [
        'advlist', 'autolink', 'lists', 'link',
        'charmap', 'preview', 'searchreplace',
        'visualblocks', 'code', 'fullscreen',
        'table', 'wordcount'
    ],

    // Barre d'outils sur 2 lignes
    toolbar:
        'undo redo | blocks | bold italic underline | ' +
        'alignleft aligncenter alignright | ' +
        'bullist numlist | link | removeformat | code fullscreen',

    // Formats de blocs — h1 retiré volontairement (1 seul h1 par page)
    block_formats:
        'Paragraphe=p;' +
        'Titre 2 (section)=h2;' +
        'Titre 3 (sous-section)=h3;' +
        'Titre 4=h4;' +
        'Citation=blockquote;' +
        'Code=pre',

    // CSS injecté dans l'iframe de l'éditeur
    content_style: `
        body {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 16px;
            line-height: 1.7;
            color: #1a1a1a;
            padding: 12px 16px;
            max-width: 100%;
        }
        h2 { font-size: 1.4rem; margin-top: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: .3rem; }
        h3 { font-size: 1.2rem; margin-top: 1.2rem; }
        h4 { font-size: 1.05rem; }
        blockquote { border-left: 3px solid #ccc; margin-left: 0; padding-left: 1rem; color: #555; }
        img { max-width: 100%; height: auto; }
        table { border-collapse: collapse; width: 100%; }
        td, th { border: 1px solid #ddd; padding: 6px 10px; }
    `,

    // Synchronisation automatique vers le textarea à chaque changement
    setup: function(editor) {
        editor.on('change input keyup', function() {
            editor.save();
        });
    }
});

// Synchroniser TinyMCE → textarea juste avant soumission
document.getElementById('article-form').addEventListener('submit', function() {
    tinymce.triggerSave();
});

// Aperçu image
function previewImage(input) {
    const p = document.getElementById('image-preview');
    if (input.files && input.files[0]) {
        const r = new FileReader();
        r.onload = e => { p.src = e.target.result; p.style.display = 'block'; };
        r.readAsDataURL(input.files[0]);
    }
}

// Afficher / masquer le champ date selon le statut
function toggleDatePublication() {
    const statut    = document.getElementById('statut').value;
    const container = document.getElementById('date-publication-container');
    container.style.display = (statut === 'planifie') ? 'block' : 'none';
}
</script>

<?php require '../includes/footer.php'; ?>