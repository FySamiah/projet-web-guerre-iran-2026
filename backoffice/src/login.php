<?php
// backoffice/src/login.php

if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['user'])) {
    header('Location: /dashboard.php');
    exit;
}

require_once 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $user = getUserByEmail($email);
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'    => $user['id'],
                'email' => $user['email'],
                'role'  => $user['role'],
            ];
            header('Location: /dashboard.php');
            exit;
        }
        $error = 'Email ou mot de passe incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — Back-office</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-box {
            background: #fff;
            border-radius: 12px;
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            border: 1px solid #e3e6ef;
        }
        .login-box h1 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: .2rem;
        }
        .login-box .sub {
            font-size: 13px;
            color: #888;
            margin-bottom: 1.8rem;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h1>Guerre en Iran</h1>
    <p class="sub">Back-office — Connexion</p>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2" style="font-size:13px">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label class="form-label fw-medium" for="email">Email</label>
            <input
                type="email" id="email" name="email"
                class="form-control"
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                placeholder="admin@site.com"
                required autofocus
            >
        </div>
        <div class="mb-4">
            <label class="form-label fw-medium" for="password">Mot de passe</label>
            <input
                type="password" id="password" name="password"
                class="form-control"
                placeholder="••••••••"
                required
            >
        </div>
        <button type="submit" class="btn btn-dark w-100">
            Se connecter
        </button>
    </form>
</div>
</body>
</html>