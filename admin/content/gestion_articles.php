<?php
/**
 * gestion_articles.php - CRUD complet des articles (admin)
 */
$articleDAO = new ArticleDAO();
$catDAO     = new CategorieArticleDAO();
$categories = $catDAO->findAll();
$articles   = $articleDAO->findAll();
$success = $erreur = '';
$article_edit = null;

// Suppression (soft delete)
if (isset($_GET['supprimer'])) {
    $articleDAO->delete((int)$_GET['supprimer']);
    $success = "Article désactivé.";
    $articles = $articleDAO->findAll();
}

// Édition - préchargement du formulaire
if (isset($_GET['editer'])) {
    $article_edit = $articleDAO->findById((int)$_GET['editer']);
}

// Traitement formulaire (insert ou update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id      = (int)($_POST['id_article'] ?? 0);
    $libelle = trim($_POST['libelle'] ?? '');
    $prix    = (float)($_POST['prix_unitaire'] ?? 0);
    $taille  = trim($_POST['taille'] ?? '');
    $couleur = trim($_POST['couleur'] ?? '');
    $marque  = trim($_POST['marque'] ?? '');
    $stock   = (int)($_POST['stock'] ?? 0);
    $id_cat  = (int)($_POST['id_categorie'] ?? 0);
    $code    = trim($_POST['code_article'] ?? '');
    $photo   = trim($_POST['photo_principale'] ?? '');

    if (!$libelle || !$prix || !$id_cat) {
        $erreur = "Libellé, prix et catégorie sont obligatoires.";
    } else {
        try {
            if ($id > 0) {
                // Update
                $a = $articleDAO->findById($id);
                $a->setLibelle($libelle);
                $a->setPrix($prix);
                $a->setTaille($taille);
                $a->setCouleur($couleur);
                $a->setMarque($marque);
                $a->setStock($stock);
                $a->setActif(isset($_POST['actif']));
                $a->setPhoto($photo);
                $articleDAO->update($a);
                $success = "Article modifié.";
            } else {
                // Insert
                $a = new Article(0, $code, $libelle, $photo, $prix, $taille, $couleur, $marque, $stock, true, $id_cat);
                $articleDAO->insert($a);
                $success = "Article ajouté.";
            }
            $articles = $articleDAO->findAll();
            $article_edit = null;
        } catch (Exception $e) {
            $erreur = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<h2 class="mb-4"><i class="bi bi-box-seam"></i> Gestion des articles</h2>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($erreur):  ?><div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>

<div class="row">
    <!-- Formulaire -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">
                <?= $article_edit ? '✏️ Modifier l\'article' : '➕ Ajouter un article' ?>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="id_article" value="<?= $article_edit?->getIdArticle() ?? 0 ?>">

                    <?php if (!$article_edit): ?>
                    <div class="mb-2">
                        <label class="form-label" for="code_article">Code article *</label>
                        <input type="text" name="code_article" id="code_article" class="form-control form-control-sm" required>
                    </div>
                    <?php endif; ?>

                    <div class="mb-2">
                        <label class="form-label" for="libelle">Libellé *</label>
                        <input type="text" name="libelle" id="libelle" class="form-control form-control-sm" required
                               value="<?= htmlspecialchars($article_edit?->getLibelle() ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="prix_unitaire">Prix unitaire (€) *</label>
                        <input type="number" step="0.01" name="prix_unitaire" id="prix_unitaire" class="form-control form-control-sm" required
                               value="<?= $article_edit?->getPrixUnitaire() ?? '' ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="id_categorie">Catégorie *</label>
                        <select name="id_categorie" id="id_categorie" class="form-select form-select-sm" required>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->getIdCategorie() ?>"
                                    <?= ($article_edit?->getIdCategorie() == $cat->getIdCategorie()) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat->getNomCategorie()) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="taille">Taille</label>
                        <input type="text" name="taille" id="taille" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($article_edit?->getTaille() ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="couleur">Couleur</label>
                        <input type="text" name="couleur" id="couleur" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($article_edit?->getCouleur() ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="marque">Marque</label>
                        <input type="text" name="marque" id="marque" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($article_edit?->getMarque() ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="stock">Stock</label>
                        <input type="number" name="stock" id="stock" class="form-control form-control-sm" min="0"
                               value="<?= $article_edit?->getStock() ?? 0 ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="photo_principale">Photo (nom du fichier)</label>
                        <input type="text" name="photo_principale" id="photo_principale" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($article_edit?->getPhoto() ?? '') ?>"
                               placeholder="ex: jean_slim.jpg">
                    </div>
                    <?php if ($article_edit): ?>
                    <div class="mb-2 form-check">
                        <input type="checkbox" class="form-check-input" name="actif" id="actif"
                               <?= $article_edit->isActif() ? 'checked' : '' ?>>
                        <label class="form-check-label" for="actif">Article actif (visible)</label>
                    </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-warning btn-sm w-100">
                        <?= $article_edit ? 'Modifier' : 'Ajouter' ?>
                    </button>
                    <?php if ($article_edit): ?>
                    <a href="/admin/index_.php?page=gestion_articles" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                        Annuler
                    </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="col-md-8">
        <div class="table-responsive" id="tableau-articles">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th><th>Code</th><th>Libellé</th><th>Prix</th>
                        <th>Stock</th><th>Actif</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $a): ?>
                    <tr class="<?= !$a->isActif() ? 'table-secondary text-muted' : '' ?>">
                        <td><?= $a->getIdArticle() ?></td>
                        <td><?= htmlspecialchars($a->getCodeArticle()) ?></td>
                        <td><?= htmlspecialchars($a->getLibelle()) ?></td>
                        <td><?= number_format($a->getPrixUnitaire(), 2) ?> €</td>
                        <td>
                            <span class="badge <?= $a->getStock() > 0 ? 'bg-success' : 'bg-danger' ?>">
                                <?= $a->getStock() ?>
                            </span>
                        </td>
                        <td><?= $a->isActif() ? '✅' : '❌' ?></td>
                        <td>
                            <a href="?page=gestion_articles&editer=<?= $a->getIdArticle() ?>"
                               class="btn btn-warning btn-sm">✏️</a>
                            <a href="?page=gestion_articles&supprimer=<?= $a->getIdArticle() ?>"
                               class="btn btn-danger btn-sm"
                               data-confirm="Désactiver cet article ?">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>