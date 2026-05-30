<?php
/**
 * accueil.php - Page d'accueil publique
 */
require_once __DIR__ . '/../admin/src/php/classes/Connection.class.php';
require_once __DIR__ . '/../admin/src/php/classes/Article.class.php';
require_once __DIR__ . '/../admin/src/php/classes/ArticleDAO.class.php';
require_once __DIR__ . '/../admin/src/php/classes/CategorieArticleDAO.class.php';

$articleDAO = new ArticleDAO();
$articles   = $articleDAO->findActifs();
$catDAO     = new CategorieArticleDAO();
$categories = $catDAO->findAll();
// Sélection des 4 articles vedettes
$vedettes = array_slice($articles, 0, 4);
$fallbackImages = [
        'https://www.chaudici.com/images/color/Jean-Slim-Homme-Jeunesse-Slim-Tendance-Homme-Pantalon-2064-c00.jpg',
        'https://img-lcwaikiki.mncdn.com/mnresize/1020/1360/pim/productimages/20231/6278147/v1/l_20231-s39518z8-dfl-98-76-97-189_a.jpg',
        'https://robe-avenue.com/cdn/shop/files/preview_images/8e78043e6de74605aed210e2a4bab536.thumbnail.0000000000.jpg?v=1748275525&width=1000',
        'https://www.mytheresa.com/media/1094/1238/100/44/P01121090.jpg',
];

?>

<!-- BANNIÈRE DYNAMIQUE -->
<div id="bannierePrincipale" class="carousel slide mb-5" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div class="banner-slide d-flex align-items-center justify-content-center text-white text-center"
                 style="background: linear-gradient(135deg,#1a1a2e,#16213e); min-height:350px;">
                <div>
                    <h1 class="display-4 fw-bold">Nouvelle Collection 2026</h1>
                    <p class="lead">Vêtements, Chaussures &amp; Accessoires pour tous les styles</p>
                    <a href="./index_.php?page=catalogue" class="btn btn-warning btn-lg mt-2">
                        Découvrir le catalogue
                    </a>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="banner-slide d-flex align-items-center justify-content-center text-white text-center"
                 style="background: linear-gradient(135deg,#0f3460,#533483); min-height:350px;">
                <div>
                    <h1 class="display-4 fw-bold">Soldes jusqu'à -50%</h1>
                    <p class="lead">Profitez de nos offres exclusives sur la mode femme &amp; homme</p>
                    <a href="./index_.php?page=catalogue" class="btn btn-light btn-lg mt-2">
                        Voir les offres
                    </a>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#bannierePrincipale" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#bannierePrincipale" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- CATÉGORIES -->
<section class="mb-5">
    <h2 class="text-center mb-4">Nos Catégories</h2>
    <div class="row g-3 justify-content-center">
        <?php foreach ($categories as $cat): ?>
            <div class="col-6 col-md-2">
                <a href="./index_.php?page=catalogue&cat=<?= $cat->getIdCategorie() ?>"
                   class="btn btn-outline-dark w-100">
                    <?= htmlspecialchars($cat->getNomCategorie()) ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ARTICLES VEDETTES -->
<section class="mb-5">
    <h2 class="text-center mb-4">Articles Vedettes</h2>
    <div class="row g-4" id="articlesVedettes">
        <?php foreach ($vedettes as $index => $article):
            $photo = $article->getPhoto();
            $src = !empty($photo) && str_starts_with($photo, 'http')
                    ? $photo
                    : $fallbackImages[$index % count($fallbackImages)];
            ?>
            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm article-card">
                    <img src="<?= $src ?>"
                         class="card-img-top article-img"
                         alt="<?= htmlspecialchars($article->getLibelle()) ?>"
                         style="height:220px; object-fit:contain; object-position:center; width:100%; background:#f8f8f8;">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title"><?= htmlspecialchars($article->getLibelle()) ?></h6>
                        <p class="text-muted small mb-1">
                            <?= htmlspecialchars($article->getMarque()) ?> |
                            Taille : <?= htmlspecialchars($article->getTaille()) ?>
                        </p>
                        <p class="fw-bold text-success fs-5 mt-auto">
                            <?= number_format($article->getPrixUnitaire(), 2) ?> €
                        </p>
                        <a href="./index_.php?page=article_detail&id=<?= $article->getIdArticle() ?>"
                           class="btn btn-dark btn-sm mt-2">Voir le produit</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- AVANTAGES -->
<section class="row g-3 text-center mb-4">
    <div class="col-md-4">
        <div class="p-3 border rounded">
            <i class="bi bi-truck fs-2 text-warning"></i>
            <h6 class="mt-2">Livraison Mondiale</h6>
            <p class="small text-muted">Expédition dans le monde entier</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-3 border rounded">
            <i class="bi bi-arrow-counterclockwise fs-2 text-warning"></i>
            <h6 class="mt-2">Retours 30 jours</h6>
            <p class="small text-muted">Satisfait ou remboursé</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-3 border rounded">
            <i class="bi bi-shield-check fs-2 text-warning"></i>
            <h6 class="mt-2">Paiement Sécurisé</h6>
            <p class="small text-muted">Transactions 100% sécurisées</p>
        </div>
    </div>
</section>