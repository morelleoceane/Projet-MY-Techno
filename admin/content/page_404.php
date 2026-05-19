<?php
/**
 * page_404.php - Page introuvable (admin)
 */
http_response_code(404);
?>
<div class="text-center py-5">
    <h1 class="display-1 fw-bold text-muted">404</h1>
    <h2>Page introuvable</h2>
    <p class="text-muted">La page d'administration demandée n'existe pas.</p>
    <a href="/admin/index_.php?page=accueil" class="btn btn-danger">Retour au tableau de bord</a>
</div>
