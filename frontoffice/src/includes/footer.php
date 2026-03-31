<?php
// frontoffice/src/includes/footer.php
// Pied de page + fermeture HTML
?>

</div><!-- /.wrap -->

<!-- PIED DE PAGE -->
<footer class="site-footer" role="contentinfo">
    <div class="footer-grid">
        <div class="footer-col">
            <h4>RUBRIQUES</h4>
            <?php foreach ($categories as $cat): ?>
                <a href="/categorie/<?= htmlspecialchars($cat['slug']) ?>" title="Voir tous les articles : <?= htmlspecialchars($cat['nom']) ?>">
                    <?= htmlspecialchars($cat['nom']) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="footer-col">
            <h4>À PROPOS</h4>
            <a href="#mentions" title="Consulter les mentions légales">Mentions légales</a>
            <a href="#contact" title="Nous contacter">Contact & Publicité</a>
            <a href="#cookies" title="Gestion des cookies">Politique des cookies</a>
            <a href="#donnees" title="Confidentialité">Politique de confidentialité</a>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2026 <strong>IranInfo</strong> — Site d'informations indépendant. Tous droits réservés.</p>
    </div>
</footer>

<!-- GOOGLE ANALYTICS (À CONFIGURER) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_ID"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'GA_ID', { 'anonymize_ip': true });
</script>

<!-- JAVASCRIPT - OPTIMISÉ -->
<script src="/assets/js/main.js" defer></script>

</body>
</html>
