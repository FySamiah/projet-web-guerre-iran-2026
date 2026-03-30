<?php
// frontoffice/src/index.php
// Routeur principal - Point d'entrée unique

error_reporting(E_ALL);
ini_set('display_errors', 0);

// Connexion à la base de données + fonctions
require_once 'includes/db.php';

// Récupère la page demandée
$page = $_GET['page'] ?? 'home';

// Valide et charge la page
$pages = ['home', 'article', 'categorie'];

if (in_array($page, $pages)) {
    include 'pages/' . $page . '.php';
} else {
    // Page non trouvée
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404 — Page non trouvée</title>
        <link rel="stylesheet" href="/assets/css/style.css">
    </head>
    <body style="display:flex;align-items:center;justify-content:center;min-height:100vh;background:#f5f5f0">
        <div style="text-align:center;max-width:400px">
            <h1 style="font-size:48px;color:#e63946;margin-bottom:10px">404</h1>
            <h2 style="font-size:22px;color:#1a1a2e;margin-bottom:20px">Page non trouvée</h2>
            <p style="color:#666;margin-bottom:20px">La page que vous recherchez n'existe pas ou a été supprimée.</p>
            <a href="/" style="display:inline-block;background:#1a1a2e;color:#fff;padding:10px 20px;border-radius:4px;text-decoration:none">Retour à l'accueil</a>
        </div>
    </body>
    </html>
    <?php
}
