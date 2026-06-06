<?php
/**
 * gestion_promotions.php - CRUD promotions (admin)
 */
$promoDAO  = new PromotionDAO();
$success = $erreur = '';
$promo_edit = null;

if (isset($_GET['supprimer'])) {
    $promoDAO->delete((int)$_GET['supprimer']);
    $success = "Promotion supprimée.";
}
if (isset($_GET['editer'])) {
    $promos = $promoDAO->findAll();
    foreach ($promos as $p) {
        if ($p->getIdPromotion() == (int)$_GET['editer']) { $promo_edit = $p; break; }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int)($_POST['id_promotion'] ?? 0);
    $code = trim($_POST['code_promo'] ?? '');
    $taux = (int)($_POST['taux_remise'] ?? 0);
    if (!$code || $taux < 1 || $taux > 100) {
        $erreur = "Code et taux valide (1-100) requis.";
    } else {
        try {
            if ($id > 0) {
                $p = new Promotion($id, $code, $taux, $_SESSION['admin_id']);
                $promoDAO->update($p);
                $success = "Promotion modifiée.";
            } else {
                $p = new Promotion(0, $code, $taux, $_SESSION['admin_id']);
                $promoDAO->insert($p);
                $success = "Promotion créée.";
            }
            $promo_edit = null;
        } catch (Exception $e) {
            $erreur = "Erreur : " . $e->getMessage();
        }
    }
}

$promotions = $promoDAO->findAll();
?>

<h2 class="mb-4"><i class="bi bi-tag"></i> Gestion des promotions</h2>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($erreur):  ?><div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">
                <?= $promo_edit ? 'Modifier la promotion' : 'Nouvelle promotion' ?>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="id_promotion" value="<?= $promo_edit?->getIdPromotion() ?? 0 ?>">
                    <div class="mb-3">
                        <label class="form-label" for="code_promo">Code promo *</label>
                        <input type="text" name="code_promo" id="code_promo" class="form-control" required
                               value="<?= htmlspecialchars($promo_edit?->getCodePromo() ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="taux_remise">Taux de remise (%) *</label>
                        <input type="number" name="taux_remise" id="taux_remise" class="form-control" min="1" max="100" required
                               value="<?= $promo_edit?->getTauxRemise() ?? '' ?>">
                    </div>
                    <button type="submit" class="btn btn-warning w-100">
                        <?= $promo_edit ? 'Modifier' : 'Créer' ?>
                    </button>
                    <?php if ($promo_edit): ?>
                        <a href="?page=gestion_promotions" class="btn btn-outline-secondary btn-sm w-100 mt-2">Annuler</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr><th>ID</th><th>Code</th><th>Remise</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($promotions as $p): ?>
                <tr>
                    <td><?= $p->getIdPromotion() ?></td>
                    <td><code><?= htmlspecialchars($p->getCodePromo()) ?></code></td>
                    <td><span class="badge bg-success"><?= $p->getTauxRemise() ?>%</span></td>
                    <td>
                        <a href="?page=gestion_promotions&editer=<?= $p->getIdPromotion() ?>"
                           class="btn btn-warning btn-sm">✏️</a>
                        <a href="?page=gestion_promotions&supprimer=<?= $p->getIdPromotion() ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Supprimer cette promotion ?')">🗑️</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
