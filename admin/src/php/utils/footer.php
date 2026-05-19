<?php
/**
 * footer.php - Pied de page commun
 */
?>
<footer class="site-footer mt-5 py-4">
    <div class="container">
        <div class="row text-center text-md-start">
            <div class="col-md-4 mb-3">
                <h6 class="fw-bold text-warning">ModeShopping</h6>
                <p class="text-muted small">Vêtements, chaussures et accessoires de mode pour toute la famille.</p>
            </div>
            <div class="col-md-4 mb-3">
                <h6 class="fw-bold">Liens utiles</h6>
                <ul class="list-unstyled small">
                    <li><a href="/index_.php?page=accueil" class="text-muted text-decoration-none">Accueil</a></li>
                    <li><a href="/index_.php?page=catalogue" class="text-muted text-decoration-none">Catalogue</a></li>
                    <li><a href="/index_.php?page=cgv" class="text-muted text-decoration-none">CGV</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h6 class="fw-bold">Retours & Livraison</h6>
                <p class="text-muted small">Retours acceptés jusqu'à 30 jours après réception.<br>Livraison mondiale disponible.</p>
            </div>
        </div>
        <hr>
        <p class="text-center text-muted small mb-0">&copy; <?= date('Y') ?> ModeShopping – Projet TI2</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/admin/assets/js/app.js"></script>
</body>
</html>
