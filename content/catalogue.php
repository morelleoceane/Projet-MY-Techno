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
                <form method="GET" action="/index_.php">
                    <input type="hidden" name="page" value="catalogue">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catégorie</label>
                        <select name="cat" class="form-select form-select-sm">
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
                        <label class="form-label fw-semibold">Taille</label>
                        <select name="taille" class="form-select form-select-sm">
                            <option value="">Toutes</option>
                            <?php foreach (['XS','S','M','L','XL','XXL','36','38','40','42','44','46','Unique'] as $t): ?>
                            <option value="<?= $t ?>" <?= $taille === $t ? 'selected' : '' ?>><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Couleur</label>
                        <input type="text" name="couleur" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($couleur ?? '') ?>" placeholder="ex: Noir">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Marque</label>
                        <input type="text" name="marque" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($marque ?? '') ?>" placeholder="ex: Nike">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Prix (€)</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="pmin" class="form-control form-control-sm"
                                   placeholder="Min" value="<?= $prix_min ?? '' ?>">
                            <input type="number" name="pmax" class="form-control form-control-sm"
                                   placeholder="Max" value="<?= $prix_max ?? '' ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark btn-sm w-100">Appliquer</button>
                    <a href="/index_.php?page=catalogue" class="btn btn-outline-secondary btn-sm w-100 mt-2">
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
            <?php foreach ($articles as $article): ?>
            <div class="col-6 col-md-4">
                <div class="card h-100 shadow-sm article-card">
                    <img src="/admin/assets/images/<?= htmlspecialchars($article->getPhoto()) ?>"
                         class="card-img-top"
                         alt="<?= htmlspecialchars($article->getLibelle()) ?>"
                         onerror="this.src='/admin/assets/images/no_image.jpg'"
                         style="height:200px; object-fit:cover;">
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
                        <a href="/index_.php?page=article_detail&id=<?= $article->getIdArticle() ?>"
                           class="btn btn-dark btn-sm">Voir le produit</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
