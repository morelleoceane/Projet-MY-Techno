<?php
/**
 * header.php - En-tête HTML commun
 * Inclus dans les index.php uniquement
 */
$isAdmin = isset($_SESSION['admin_id']);
$isClient = isset($_SESSION['client_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ModeShopping – Vêtements, Chaussures &amp; Accessoires</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/admin/assets/css/style.css">
    <link rel="stylesheet" href="/admin/assets/css/custom.css">
</head>
<body>
<header class="site-header">
    <div class="container d-flex align-items-center justify-content-between py-2">
        <a href="/index_.php" class="brand-logo text-decoration-none">
            <span class="fw-bold fs-3 text-white">Mode<span class="text-warning">Shopping</span></span>
        </a>
        <div class="search-bar d-none d-md-block">
            <form action="/index_.php" method="GET" class="d-flex">
                <input type="hidden" name="page" value="catalogue">
                <input type="text" name="q" class="form-control me-2" placeholder="Rechercher un article...">
                <button class="btn btn-warning" type="submit"><i class="bi bi-search"></i></button>
            </form>
        </div>
        <div class="header-actions d-flex gap-3">
            <?php if ($isClient): ?>
                <a href="/index_.php?page=mon_compte" class="text-white"><i class="bi bi-person-circle fs-4"></i></a>
                <a href="/index_.php?page=panier" class="text-white position-relative">
                    <i class="bi bi-cart3 fs-4"></i>
                    <?php if (!empty($_SESSION['panier'])): ?>
                        <span class="badge bg-warning text-dark position-absolute top-0 start-100 translate-middle">
                            <?= count($_SESSION['panier']) ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="/index_.php?page=deconnexion" class="text-white"><i class="bi bi-box-arrow-right fs-4"></i></a>
            <?php elseif ($isAdmin): ?>
                <span class="text-warning fw-bold">Admin</span>
                <a href="/admin/index_.php?page=deconnexion_admin" class="text-white"><i class="bi bi-box-arrow-right fs-4"></i></a>
            <?php else: ?>
                <a href="/index_.php?page=connexion" class="text-white"><i class="bi bi-person fs-4"></i></a>
                <a href="/index_.php?page=panier" class="text-white"><i class="bi bi-cart3 fs-4"></i></a>
            <?php endif; ?>
        </div>
    </div>
</header>
