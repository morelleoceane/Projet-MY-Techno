<?php
/**
 * catalogue.php - Catalogue avec filtrage multicritères
 */

$articleDAO = new ArticleDAO();
$catDAO     = new CategorieArticleDAO();
$categories = $catDAO->findAll();

// Récupération des filtres
$cat      = isset($_GET['cat'])     ? (int)$_GET['cat']     : null;
$taille   = $_GET['taille']   ?? null;
$couleur  = $_GET['couleur']  ?? null;
$marque   = $_GET['marque']   ?? null;
$prix_min = isset($_GET['pmin']) && $_GET['pmin'] !== '' ? (float)$_GET['pmin'] : null;
$prix_max = isset($_GET['pmax']) && $_GET['pmax'] !== '' ? (float)$_GET['pmax'] : null;
$q        = $_GET['q']        ?? null;

// Recherche textuelle combinée avec filtres
$articles = $articleDAO->findByCriteres($cat, $taille, $couleur, $marque, $prix_min, $prix_max);

// Filtre texte libre
if ($q) {
    $articles = array_filter($articles, function($a) use ($q) {
        return stripos($a->getLibelle(), $q) !== false ||
                stripos($a->getMarque(), $q) !== false;
    });
}

// CORRECTION 1 : fallback images
$fallbackImages = [
        'https://www.chaudici.com/images/color/Jean-Slim-Homme-Jeunesse-Slim-Tendance-Homme-Pantalon-2064-c00.jpg',
        'https://img-lcwaikiki.mncdn.com/mnresize/1020/1360/pim/productimages/20231/6278147/v1/l_20231-s39518z8-dfl-98-76-97-189_a.jpg',
        'https://robe-avenue.com/cdn/shop/files/preview_images/8e78043e6de74605aed210e2a4bab536.thumbnail.0000000000.jpg?v=1748275525&width=1000',
        'https://www.mytheresa.com/media/1094/1238/100/44/P01121090.jpg',
];
?>

<h2 class="mb-4">Catalogue <span class="text-muted fs-5">(<?= count($articles) ?> article(s))</span></h2>

<div class="row">
    <!-- Filtres -->
    <aside class="col-md-3 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">
                <i class="bi bi-funnel"></i> Filtrer
            </div>
            <div class="card-body">
                <form method="GET" action="./index_.php">
                    <input type="hidden" name="page" value="catalogue">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="cat">Catégorie</label>
                        <select name="cat" id="cat" class="form-select form-select-sm">
                            <option value="">Toutes</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c->getIdCategorie() ?>"
                                        <?= $cat == $c->getIdCategorie() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c->getNomCategorie()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="taille">Taille</label>
                        <select name="taille" id="taille" class="form-select form-select-sm">
                            <option value="">Toutes</option>
                            <?php foreach (['XS','S','M','L','XL','XXL','36','38','40','39','43','42','44','46','Unique'] as $t): ?>
                                <option value="<?= $t ?>" <?= $taille === $t ? 'selected' : '' ?>><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="couleur">Couleur</label>
                        <input type="text" name="couleur" id="couleur" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($couleur ?? '') ?>" placeholder="ex: Noir">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="marque">Marque</label>
                        <input type="text" name="marque" id="marque" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($marque ?? '') ?>" placeholder="ex: Nike">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Prix (€)</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="pmin" id="pmin" class="form-control form-control-sm"
                                   aria-label="Prix minimum" placeholder="Min" value="<?= $prix_min ?? '' ?>">
                            <input type="number" name="pmax" id="pmax" class="form-control form-control-sm"
                                   aria-label="Prix maximum" placeholder="Max" value="<?= $prix_max ?? '' ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark btn-sm w-100">Appliquer</button>
                    <a href="./index_.php?page=catalogue" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                        Réinitialiser
                    </a>
                </form>
            </div>
        </div>
    </aside>

    <!-- Grille articles -->
    <div class="col-md-9">
        <?php if (empty($articles)): ?>
            <div class="alert alert-info">Aucun article trouvé pour ces critères.</div>
        <?php else: ?>
            <div class="row g-3" id="grille-articles">
                <?php foreach ($articles as $article):
                    // CORRECTION 2 : utilise l'URL de la BD ou le fallback
                    $photo = $article->getPhoto();
                    $imgSrc = !empty($photo) && str_starts_with($photo, 'http')
                            ? $photo
                            : $fallbackImages[$article->getIdArticle() % count($fallbackImages)];
                    ?>
                    <div class="col-6 col-md-4">
                        <div class="card h-100 shadow-sm article-card">
                            <img src="<?= $imgSrc ?>"
                                 class="card-img-top article-img article-img-thumb"
                                 alt="<?= htmlspecialchars($article->getLibelle()) ?>">
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title"><?= htmlspecialchars($article->getLibelle()) ?></h6>
                                <p class="text-muted small">
                                    <?= htmlspecialchars($article->getMarque() ?: '—') ?> |
                                    <?= htmlspecialchars($article->getCouleur() ?: '—') ?> |
                                    T.<?= htmlspecialchars($article->getTaille() ?: '—') ?>
                                </p>
                                <p class="fw-bold text-success fs-5 mt-auto">
                                    <?= number_format($article->getPrixUnitaire(), 2) ?> €
                                </p>
                                <?php if ($article->getStock() === 0): ?>
                                    <span class="badge bg-danger mb-2">Rupture de stock</span>
                                <?php endif; ?>
                                <a href="./index_.php?page=article_detail&id=<?= $article->getIdArticle() ?>"
                                   class="btn btn-dark btn-sm">Voir le produit</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php // CORRECTION : le script de fallback image est désormais dans admin/assets/js/app.js (initArticleImageFallback) ?>