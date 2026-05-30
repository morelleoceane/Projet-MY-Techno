<?php
/**
 * commande.php - Validation de la commande (client connecté requis)
 */
require_once __DIR__ . '/../admin/src/php/utils/check_connection.php';
checkClientConnecte();

$panier = $_SESSION['panier'] ?? [];
if (empty($panier)) {
    header('Location: ./index_.php?page=panier');
    exit();
}

$erreur  = '';
$success = '';
$clientDAO  = new ClientDAO();
$commandeDAO = new CommandeDAO();
$client = $clientDAO->findById($_SESSION['client_id']);

// Calcul total
$total = 0;
foreach ($panier as $item) {
    $total += $item['prix_unitaire'] * $item['quantite'];
}
$remise = 0;
if (isset($_SESSION['promo_taux'])) {
    $remise = $total * $_SESSION['promo_taux'] / 100;
}
$total_final = $total - $remise;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adresse       = trim($_POST['adresse_livraison'] ?? '');
    $type_livraison = isset($_POST['type_livraison']);
    $cgv_ok        = isset($_POST['cgv_ok']);

    if (!$adresse) {
        $erreur = "Veuillez saisir une adresse de livraison.";
    } elseif (!$cgv_ok) {
        $erreur = "Vous devez accepter les CGV pour confirmer la commande.";
    } else {
        try {
            $commande = new Commande(0, '', $type_livraison, '', $adresse, 'en_attente', $client->getIdClient(),$total_final);
            $idCmd = $commandeDAO->insert($commande);
            // Insérer les lignes
            foreach ($panier as $item) {
                $commandeDAO->insertLigne($idCmd, $item['id_article'], $item['quantite'], $item['prix_unitaire']);
            }
            // Vider le panier
            unset($_SESSION['panier'], $_SESSION['promo_code'], $_SESSION['promo_taux']);
            $success = "✅ Commande #$idCmd enregistrée avec succès ! Merci pour votre achat.";
        } catch (Exception $e) {
            $erreur = "Erreur lors de la commande : " . $e->getMessage();
        }
    }
}
?>

<h2 class="mb-4"><i class="bi bi-bag-check"></i> Confirmer ma commande</h2>

<?php if ($success): ?>
    <div class="alert alert-success fs-5">
        <?= htmlspecialchars($success) ?>
        <br><a href="/index_.php?page=historique_commandes" class="btn btn-dark mt-2">Voir mes commandes</a>
    </div>
<?php else: ?>

<?php if ($erreur): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-7">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">
                <i class="bi bi-truck"></i> Adresse et livraison
            </div>
            <div class="card-body">
                <form method="POST" id="form-commande">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Adresse de livraison *</label>
                        <textarea name="adresse_livraison" class="form-control" rows="3" required
                                  placeholder="Numéro, rue, ville, pays"><?= htmlspecialchars($client->getAdresse()) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="type_livraison" id="express">
                            <label class="form-check-label" for="express">Livraison express (+5€)</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cgv_ok" id="cgv_cmd" required>
                            <label class="form-check-label" for="cgv_cmd">
                                J'accepte les <a href="/index_.php?page=cgv" target="_blank">CGV</a> *
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold">
                        <i class="bi bi-lock"></i> Confirmer et payer
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">Récapitulatif</div>
            <div class="card-body">
                <?php foreach ($panier as $item): ?>
                <div class="d-flex justify-content-between mb-1">
                    <span><?= htmlspecialchars($item['libelle']) ?> ×<?= $item['quantite'] ?></span>
                    <span><?= number_format($item['prix_unitaire'] * $item['quantite'], 2) ?> €</span>
                </div>
                <?php endforeach; ?>
                <hr>
                <?php if ($remise > 0): ?>
                <div class="d-flex justify-content-between text-success">
                    <span>Remise (<?= $_SESSION['promo_taux'] ?>%)</span>
                    <span>-<?= number_format($remise, 2) ?> €</span>
                </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between fw-bold fs-5 mt-2">
                    <span>Total</span>
                    <span class="text-success"><?= number_format($total_final, 2) ?> €</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
