<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$pageTitle = 'Médiathèque — Back-office';
$uploadDir = __DIR__ . '/../uploads/';
$errors    = [];
$success   = '';

// Upload d'une nouvelle image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    if (!empty($_FILES['image']['name'])) {
        $filename = uploadImage($_FILES['image']);
        if ($filename) {
            $success = 'Image uploadée avec succès.';
        } else {
            $errors[] = 'Image invalide (JPG/PNG/WebP/GIF, max 5 Mo).';
        }
    } else {
        $errors[] = 'Veuillez sélectionner une image.';
    }
}

// Suppression d'une image
if (isset($_GET['delete'])) {
    $filename = basename($_GET['delete']);
    $filepath = $uploadDir . $filename;
    if (file_exists($filepath) && is_file($filepath)) {
        if (unlink($filepath)) {
            header('Location: /admin/media/list.php?success=deleted');
            exit;
        }
    }
    header('Location: /admin/media/list.php?error=delete_failed');
    exit;
}

// Récupérer les images
$images = [];
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === '.gitkeep' || $file === '.htaccess') continue;
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $filepath = $uploadDir . $file;
            $images[] = [
                'name'     => $file,
                'size'     => filesize($filepath),
                'date'     => filemtime($filepath),
                'url'      => '/uploads/' . $file,
            ];
        }
    }
    // Trier par date (plus récent en premier)
    usort($images, fn($a, $b) => $b['date'] - $a['date']);
}

require '../includes/nav.php';
?>

<div class="page-header">
    <h1>Médiathèque <span class="text-secondary fw-normal fs-6">(<?= count($images) ?> images)</span></h1>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger"><ul class="mb-0 ps-3"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
    <div class="alert alert-success">Image supprimée.</div>
<?php endif; ?>

<!-- Upload d'image -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <strong style="font-size:14px">Uploader une image</strong>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label" for="image">Sélectionner une image</label>
                <input type="file" id="image" name="image" class="form-control"
                       accept="image/jpeg,image/png,image/webp,image/gif" required>
                <div class="form-text">Formats acceptés : JPG, PNG, WebP, GIF. Taille max : 5 Mo.</div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark">Uploader</button>
            </div>
        </form>
    </div>
</div>

<!-- Galerie d'images -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <strong style="font-size:14px">Images uploadées</strong>
    </div>
    <div class="card-body">
        <?php if (empty($images)): ?>
            <p class="text-center text-secondary py-4">Aucune image uploadée.</p>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($images as $img): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card h-100 border">
                            <img src="<?= htmlspecialchars($img['url']) ?>"
                                 alt="<?= htmlspecialchars($img['name']) ?>"
                                 class="card-img-top"
                                 style="height:150px;object-fit:cover;cursor:pointer"
                                 onclick="showImageModal('<?= htmlspecialchars($img['url']) ?>', '<?= htmlspecialchars($img['name']) ?>')">
                            <div class="card-body p-2">
                                <p class="card-text text-truncate mb-1" style="font-size:12px" title="<?= htmlspecialchars($img['name']) ?>">
                                    <?= htmlspecialchars($img['name']) ?>
                                </p>
                                <p class="card-text text-secondary mb-2" style="font-size:11px">
                                    <?= number_format($img['size'] / 1024, 1) ?> Ko — <?= date('d/m/Y', $img['date']) ?>
                                </p>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary flex-grow-1"
                                            onclick="copyToClipboard('<?= htmlspecialchars($img['url']) ?>')">
                                        Copier URL
                                    </button>
                                    <a href="/admin/media/list.php?delete=<?= urlencode($img['name']) ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Supprimer cette image ?')">
                                        Suppr.
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal pour afficher l'image en grand -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imageModalImg" src="" alt="" style="max-width:100%;max-height:70vh">
            </div>
        </div>
    </div>
</div>

<script>
function showImageModal(url, name) {
    document.getElementById('imageModalImg').src = url;
    document.getElementById('imageModalTitle').textContent = name;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('URL copiée !');
    });
}
</script>

<?php require '../includes/footer.php'; ?>
