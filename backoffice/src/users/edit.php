<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Seuls les admins peuvent gerer les utilisateurs
if ($currentUser['role'] !== 'admin') {
    header('Location: /admin/dashboard.php');
    exit;
}

$id   = (int)($_GET['id'] ?? 0);
$user = getUserById($id);

if (!$user) {
    header('Location: /admin/users/list.php?error=not_found');
    exit;
}

$pageTitle = 'Modifier utilisateur — Back-office';
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom          = trim($_POST['nom'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $role         = in_array($_POST['role'] ?? '', ['admin', 'editeur', 'redacteur']) ? $_POST['role'] : 'redacteur';
    $actif        = isset($_POST['actif']) ? 1 : 0;

    if ($nom === '')   $errors[] = 'Le nom est obligatoire.';
    if ($email === '') $errors[] = 'L\'email est obligatoire.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
    if ($mot_de_passe !== '' && strlen($mot_de_passe) < 6) {
        $errors[] = 'Le mot de passe doit contenir au moins 6 caracteres.';
    }

    // Verifier si l'email existe deja pour un autre utilisateur
    $existingUser = getUserByEmail($email);
    if ($existingUser && $existingUser['id'] !== $id) {
        $errors[] = 'Cet email est deja utilise.';
    }

    if (empty($errors)) {
        $ok = updateUser($id, [
            'nom'          => $nom,
            'email'        => $email,
            'mot_de_passe' => $mot_de_passe,
            'role'         => $role,
            'actif'        => $actif,
        ]);
        if ($ok) {
            header('Location: /admin/users/list.php?success=updated');
            exit;
        }
        $errors[] = 'Erreur lors de la mise a jour.';
    }
}

require '../includes/nav.php';
?>

<div class="page-header">
    <h1>Modifier utilisateur</h1>
    <a href="/admin/users/list.php" class="btn btn-outline-secondary btn-sm">← Retour</a>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="nom">Nom <span class="text-danger">*</span></label>
                    <input type="text" id="nom" name="nom" class="form-control"
                           value="<?= htmlspecialchars($_POST['nom'] ?? $user['nom']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($_POST['email'] ?? $user['email']) ?>" required>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label" for="mot_de_passe">Nouveau mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control">
                    <div class="form-text">Laisser vide pour ne pas modifier. Minimum 6 caracteres.</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="role">Role</label>
                    <select id="role" name="role" class="form-select">
                        <option value="redacteur" <?= (($_POST['role'] ?? $user['role']) === 'redacteur') ? 'selected' : '' ?>>Redacteur</option>
                        <option value="editeur" <?= (($_POST['role'] ?? $user['role']) === 'editeur') ? 'selected' : '' ?>>Editeur</option>
                        <option value="admin" <?= (($_POST['role'] ?? $user['role']) === 'admin') ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" id="actif" name="actif" class="form-check-input"
                               <?= (($_POST['actif'] ?? $user['actif']) ? 'checked' : '') ?>>
                        <label class="form-check-label" for="actif">Compte actif</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-dark">Enregistrer</button>
                <a href="/admin/users/list.php" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
