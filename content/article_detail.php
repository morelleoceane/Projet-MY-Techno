<?php
/**
 * article_detail.php - Fiche produit
 */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$articleDAO = new ArticleDAO();
$article = $articleDAO->findById($id);

if (!$article || !$article->isActif()) {
    echo '<div class="alert alert-danger">Article introuvable.</div>';
    return;
}

// Ajout au panier (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_panier'])) {
    if (!isset($_SESSION['client_id'])) {
        header('Location: /index_.php?page=connexion&redirect=article_detail&id=' . $id);
        exit();
    }
    $qte = max(1, (int)($_POST['quantite'] ?? 1));
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }
    $key = $id;
    if (isset($_SESSION['panier'][$key])) {
        $_SESSION['panier'][$key]['quantite'] += $qte;
    } else {
        $_SESSION['panier'][$key] = [
            'id_article'    => $article->getIdArticle(),
            'libelle'       => $article->getLibelle(),
            'prix_unitaire' => $article->getPrixUnitaire(),
            'photo'         => $article->getPhoto(),
            'quantite'      => $qte,
        ];
    }
    $success = "Article ajouté au panier !";
}
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/index_.php">Accueil</a></li>
        <li class="breadcrumb-item"><a href="/index_.php?page=catalogue">Catalogue</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($article->getLibelle()) ?></li>
    </ol>
</nav>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-5">
        <img src="/admin/assets/images/<?= htmlspecialchars($article->getPhoto()) ?>"
             class="img-fluid rounded shadow"
             alt="<?= htmlspecialchars($article->getLibelle()) ?>"
             onerror="this.src='/admin/assets/images/no_image.jpg'"
             style="max-height:450px; width:100%; object-fit:cover;">
    </div>
    <div class="col-md-7">
        <h1 class="h2 mb-2"><?= htmlspecialchars($article->getLibelle()) ?></h1>
        <p class="text-muted">Code : <?= htmlspecialchars($article->getCodeArticle()) ?></p>
        <hr>
        <p><strong>Marque :</strong> <?= htmlspecialchars($article->getMarque() ?: 'Non renseignée') ?></p>
        <p><strong>Couleur :</strong> <?= htmlspecialchars($article->getCouleur() ?: 'Non renseignée') ?></p>
        <p><strong>Taille :</strong> <?= htmlspecialchars($article->getTaille() ?: 'Non renseignée') ?></p>
        <p><strong>Stock :</strong>
            <?php if ($article->getStock() > 0): ?>
                <span class="badge bg-success"><?= $article->getStock() ?> en stock</span>
            <?php else: ?>
                <span class="badge bg-danger">Rupture de stock</span>
            <?php endif; ?>
        </p>
        <div class="fs-2 fw-bold text-success my-3">
            <?= number_format($article->getPrixUnitaire(), 2) ?> €
        </div>

        <?php if ($article->getStock() > 0): ?>
        <form method="POST">
            <div class="d-flex align-items-center gap-3 mb-3">
                <label for="quantite" class="fw-bold">Quantité :</label>
                <input type="number" id="quantite" name="quantite"
                       class="form-control w-auto" min="1"
                       max="<?= $article->getStock() ?>" value="1">
            </div>
            <button type="submit" name="ajouter_panier" class="btn btn-warning btn-lg">
                <i class="bi bi-cart-plus"></i> Ajouter au panier
            </button>
        </form>
        <?php else: ?>
            <button class="btn btn-secondary btn-lg" disabled>Article indisponible</button>
        <?php endif; ?>
    </div>
</div>
