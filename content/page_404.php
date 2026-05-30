<?php
/**
 * page_404.php - Page introuvable
 */
http_response_code(404);
?>
<div class="text-center py-5">
    <h1 class="display-1 fw-bold text-muted">404</h1>
    <h2>Page introuvable</h2>
    <p class="text-muted">La page que vous recherchez n'existe pas ou a été déplacée.</p>
    <a href="./index_.php" class="btn btn-dark">Retour à l'accueil</a>
</div>
