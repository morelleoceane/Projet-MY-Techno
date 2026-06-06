<?php
/**
 * admin_menu.php - Navigation administration
 */
?>
<nav class="navbar navbar-expand-md navbar-dark bg-danger">
    <div class="container">
        <span class="navbar-brand fw-bold"><i class="bi bi-shield-lock"></i> Administration</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto gap-2">
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['page']??'')==='accueil'?'active':'' ?>"
                       href="<?= BASE_URL ?>admin/index_.php?page=accueil">Tableau de bord</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['page']??'')==='gestion_articles'?'active':'' ?>"
                       href="<?= BASE_URL ?>admin/index_.php?page=gestion_articles">Articles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['page']??'')==='gestion_commandes'?'active':'' ?>"
                       href="<?= BASE_URL ?>admin/index_.php?page=gestion_commandes">Commandes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['page']??'')==='gestion_clients'?'active':'' ?>"
                       href="<?= BASE_URL ?>admin/index_.php?page=gestion_clients">Clients</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['page']??'')==='gestion_promotions'?'active':'' ?>"
                       href="<?= BASE_URL ?>admin/index_.php?page=gestion_promotions">Promotions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       href="<?= BASE_URL ?>admin/index_.php?page=deconnexion_admin">Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>