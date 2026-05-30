<?php
/**
 * gestion_commandes.php - Suivi et gestion des commandes (admin)
 */
$commandeDAO = new CommandeDAO();
$clientDAO   = new ClientDAO();
$success = $erreur = '';

// Changement de statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changer_statut'])) {
    $idCmd  = (int)$_POST['id_commande'];
    $statut = $_POST['statut'];
    $statutsValides = ['en_attente','validee','expedie','livre','annulee'];
    if (in_array($statut, $statutsValides)) {
        $commandeDAO->updateStatut($idCmd, $statut);
        $success = "Statut de la commande #$idCmd mis à jour.";
    } else {
        $erreur = "Statut invalide.";
    }
}

$commandes = $commandeDAO->findAll();
?>

<h2 class="mb-4"><i class="bi bi-list-check"></i> Gestion des commandes</h2>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($erreur):  ?><div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>

<div class="table-responsive">
    <table class="table table-hover align-middle" id="tableau-commandes">
        <thead class="table-dark">
            <tr>
                <th>#</th><th>Client</th><th>Date</th>
                <th>Adresse livraison</th><th>Statut actuel</th><th>Changer statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commandes as $cmd):
                $client = $clientDAO->findById($cmd->getIdClient());
                $statutBadge = match($cmd->getStatut()) {
                    'en_attente' => 'warning text-dark',
                    'validee'    => 'primary',
                    'expedie'    => 'info text-dark',
                    'livre'      => 'success',
                    'annulee'    => 'danger',
                    default      => 'secondary'
                };
            ?>
            <tr>
                <td><?= $cmd->getIdCommande() ?></td>
                <td>
                    <?php if ($client): ?>
                        <?= htmlspecialchars($client->getPrenomClient() . ' ' . $client->getNomClient()) ?>
                        <br><small class="text-muted"><?= htmlspecialchars($client->getAdresseEmail()) ?></small>
                    <?php else: ?>
                        <span class="text-muted">Client supprimé</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($cmd->getDateCommande()) ?></td>
                <td class="small"><?= htmlspecialchars($cmd->getAdresseLivraison()) ?></td>
                <td>
                    <span class="badge bg-<?= $statutBadge ?>">
                        <?= ucfirst($cmd->getStatut()) ?>
                    </span>
                </td>
                <td>
                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="id_commande" value="<?= $cmd->getIdCommande() ?>">
                        <select name="statut" id="statut_<?= $cmd->getIdCommande() ?>"
                                class="form-select form-select-sm"
                                aria-label="Changer le statut de la commande #<?= $cmd->getIdCommande() ?>">
                            <?php foreach (['en_attente','validee','expedie','livre','annulee'] as $s): ?>
                                <option value="<?= $s ?>" <?= $cmd->getStatut() === $s ? 'selected' : '' ?>>
                                    <?= ucfirst($s) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="changer_statut" class="btn btn-sm btn-dark">OK</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
