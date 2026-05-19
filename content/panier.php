<?php
/**
 * panier.php - Gestion du panier (session)
 */
// Suppression d'un article du panier
if (isset($_GET['supprimer'])) {
    $idSupp = (int)$_GET['supprimer'];
    unset($_SESSION['panier'][$idSupp]);
    header('Location: /index_.php?page=panier');
    exit();
}

// Mise à jour des quantités
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_panier'])) {
    foreach ($_POST['quantite'] as $idArt => $qte) {
        $qte = max(1, (int)$qte);
        if (isset($_SESSION['panier'][(int)$idArt])) {
            $_SESSION['panier'][(int)$idArt]['quantite'] = $qte;
        }
    }
    header('Location: /index_.php?page=panier');
    exit();
}

$panier = $_SESSION['panier'] ?? [];
$total  = 0;
foreach ($panier as $item) {
    $total += $item['prix_unitaire'] * $item['quantite'];
}

// Appliquer code promo
$remise = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appliquer_promo'])) {
    $code = trim($_POST['code_promo'] ?? '');
    if ($code) {
        $promoDAO = new PromotionDAO();
        $promo = $promoDAO->findByCode($code);
        if ($promo) {
            $_SESSION['promo_code']  = $promo->getCodePromo();
            $_SESSION['promo_taux']  = $promo->getTauxRemise();
        } else {
            $erreur_promo = "Code promo invalide.";
        }
    }
}
if (isset($_SESSION['promo_taux'])) {
    $remise = $total * $_SESSION['promo_taux'] / 100;
}
$total_final = $total - $remise;
?>

<h2 class="mb-4"><i class="bi bi-cart3"></i> Mon Panier</h2>

<?php if (empty($panier)): ?>
    <div class="alert alert-info">Votre panier est vide. <a href="/index_.php?page=catalogue">Continuer les achats</a></div>
<?php else: ?>

<div class="row">
    <div class="col-md-8">
        <form method="POST">
            <div class="table-responsive">
                <table class="table align-middle" id="tableau-panier">
                    <thead class="table-dark">
                        <tr>
                            <th>Article</th>
                            <th>Prix unitaire</th>
                            <th>Quantité</th>
                            <th>Sous-total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($panier as $idArt => $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="/admin/assets/images/<?= htmlspecialchars($item['photo']) ?>"
                                         width="50" height="50" style="object-fit:cover;"
                                         onerror="this.src='/admin/assets/images/no_image.jpg'">
                                    <?= htmlspecialchars($item['libelle']) ?>
                                </div>
                            </td>
                            <td><?= number_format($item['prix_unitaire'], 2) ?> €</td>
                            <td>
                                <input type="number" name="quantite[<?= $idArt ?>]"
                                       value="<?= $item['quantite'] ?>"
                                       class="form-control form-control-sm w-auto" min="1">
                            </td>
                            <td class="fw-bold">
                                <?= number_format($item['prix_unitaire'] * $item['quantite'], 2) ?> €
                            </td>
                            <td>
                                <a href="/index_.php?page=panier&supprimer=<?= $idArt ?>"
                                   class="btn btn-outline-danger btn-sm"
                                   onclick="return confirm('Supprimer cet article ?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="submit" name="update_panier" class="btn btn-secondary btn-sm mb-3">
                <i class="bi bi-arrow-clockwise"></i> Mettre à jour le panier
            </button>
        </form>
    </div>

    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-dark text-white fw-bold">Récapitulatif</div>
            <div class="card-body">
                <p>Sous-total : <strong><?= number_format($total, 2) ?> €</strong></p>

                <!-- Code promo -->
                <form method="POST" class="mb-3">
                    <div class="input-group input-group-sm">
                        <input type="text" name="code_promo" class="form-control"
                               placeholder="Code promo"
                               value="<?= htmlspecialchars($_SESSION['promo_code'] ?? '') ?>">
                        <button class="btn btn-outline-dark" type="submit" name="appliquer_promo">
                            Appliquer
                        </button>
                    </div>
                    <?php if (isset($erreur_promo)): ?>
                        <div class="text-danger small mt-1"><?= $erreur_promo ?></div>
                    <?php elseif (isset($_SESSION['promo_taux'])): ?>
                        <div class="text-success small mt-1">
                            Code appliqué : -<?= $_SESSION['promo_taux'] ?>%
                            (-<?= number_format($remise, 2) ?> €)
                        </div>
                    <?php endif; ?>
                </form>

                <hr>
                <p class="fs-5 fw-bold">Total : <?= number_format($total_final, 2) ?> €</p>

                <?php if (isset($_SESSION['client_id'])): ?>
                    <a href="/index_.php?page=commande" class="btn btn-warning w-100 fw-bold">
                        <i class="bi bi-credit-card"></i> Passer la commande
                    </a>
                <?php else: ?>
                    <a href="/index_.php?page=connexion" class="btn btn-dark w-100">
                        Se connecter pour commander
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
