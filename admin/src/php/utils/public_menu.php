<?php
/**
 * public_menu.php - Navigation publique
 */
?>
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="publicNav">
            <ul class="navbar-nav me-auto gap-2">
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['page']??'accueil')==='accueil'?'active':'' ?>"
                       href="./index_.php?page=accueil">Accueil</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Catalogue</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="./index_.php?page=catalogue&cat=1">Homme</a></li>
                        <li><a class="dropdown-item" href="./index_.php?page=catalogue&cat=2">Femme</a></li>
                        <li><a class="dropdown-item" href="./index_.php?page=catalogue&cat=3">Chaussures</a></li>
                        <li><a class="dropdown-item" href="./index_.php?page=catalogue&cat=4">Accessoires</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./index_.php?page=panier">
                        <i class="bi bi-cart3"></i> Panier
                        <?php if (!empty($_SESSION['panier'])): ?>
                            <span class="badge bg-warning text-dark"><?= count($_SESSION['panier']) ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php if (isset($_SESSION['client_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="./index_.php?page=mon_compte">Mon Compte</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./index_.php?page=deconnexion">Déconnexion</a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="./index_.php?page=connexion">Connexion</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./index_.php?page=inscription">Inscription</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
