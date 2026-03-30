<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Seuls les admins peuvent gérer les utilisateurs
if ($currentUser['role'] !== 'admin') {
    header('Location: /dashboard.php');
    exit;
}

$pageTitle = 'Nouvel utilisateur — Back-office';
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom          = trim($_POST['nom'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $role         = in_array($_POST['role'] ?? '', ['admin', 'editeur', 'redacteur']) ? $_POST['role'] : 'redacteur';
    $actif        = isset($_POST['actif']) ? 1 : 0;

    if ($nom === '')          $errors[] = 'Le nom est obligatoire.';
    if ($email === '')        $errors[] = 'L\'email est obligatoire.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
    if ($mot_de_passe === '') $errors[] = 'Le mot de passe est obligatoire.';
    if (strlen($mot_de_passe) < 6) $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';

    // Vérifier si l'email existe déjà
    if (getUserByEmail($email)) $errors[] = 'Cet email est déjà utilisé.';

    if (empty($errors)) {
        $ok = createUser([
            'nom'          => $nom,
            'email'        => $email,
            'mot_de_passe' => $mot_de_passe,
            'role'         => $role,
            'actif'        => $actif,
        ]);
        if ($ok) {
            header('Location: /users/list.php?success=created');
            exit;
        }
        $errors[] = 'Erreur lors de la création.';
    }
}

require '../includes/nav.php';
?>

<div class="page-header">
    <h1>Nouvel utilisateur</h1>
    <a href="/users/list.php" class="btn btn-outline-secondary btn-sm">← Retour</a>
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
                           value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label" for="mot_de_passe">Mot de passe <span class="text-danger">*</span></label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
                    <div class="form-text">Minimum 6 caractères.</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="role">Rôle</label>
                    <select id="role" name="role" class="form-select">
                        <option value="redacteur" <?= ($_POST['role'] ?? '') === 'redacteur' ? 'selected' : '' ?>>Rédacteur</option>
                        <option value="editeur" <?= ($_POST['role'] ?? '') === 'editeur' ? 'selected' : '' ?>>Éditeur</option>
                        <option value="admin" <?= ($_POST['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" id="actif" name="actif" class="form-check-input"
                               <?= !isset($_POST['actif']) || $_POST['actif'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="actif">Compte actif</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-dark">Créer l'utilisateur</button>
                <a href="/users/list.php" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
