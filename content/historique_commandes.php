<?php
/**
 * historique_commandes.php
 */
require_once __DIR__ . '/../admin/src/php/utils/check_connection.php';
checkClientConnecte();

$commandeDAO = new CommandeDAO();

if (isset($_GET['annuler'])) {
    $idCmd    = (int)$_GET['annuler'];
    $commande = $commandeDAO->findById($idCmd);
    if ($commande && $commande->getIdClient() === $_SESSION['client_id']) {
        $ok = $commandeDAO->delete($idCmd);
        if ($ok) {
            $_SESSION['msg_success'] = "Commande #$idCmd annulée.";
        } else {
            $_SESSION['msg_erreur'] = "Cette commande ne peut plus être annulée (déjà expédiée).";
        }
    }
    header('Location: /ProjetMYTechno/index_.php?page=mon_compte');
    exit();
}

$commandes = $commandeDAO->findByClient($_SESSION['client_id']);
?>

    <h2 class="mb-4"><i class="bi bi-clock-history"></i> Historique de mes commandes</h2>

<?php if (isset($_SESSION['msg_success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['msg_success']) ?></div>
    <?php unset($_SESSION['msg_success']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['msg_erreur'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['msg_erreur']) ?></div>
    <?php unset($_SESSION['msg_erreur']); ?>
<?php endif; ?>

<?php if (empty($commandes)): ?>
    <div class="alert alert-info">Vous n'avez aucune commande pour l'instant.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Total</th>
                <th>Adresse</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($commandes as $cmd): ?>
                <?php
                $statutBadge = match($cmd->getStatut()) {
                    'en_attente'         => 'warning',
                    'validee', 'Validée' => 'primary',
                    'expedie', 'Expédiée'=> 'info',
                    'livre',   'Livré'   => 'success',
                    'annulee', 'Annulée' => 'danger',
                    default              => 'secondary'
                };
                $annulable = !in_array($cmd->getStatut(), ['Expédiée', 'Annulée', 'Remboursée']);
                ?>
                <tr>
                    <td>#<?= $cmd->getIdCommande() ?></td>
                    <td><?= htmlspecialchars($cmd->getDateCommande()) ?></td>
                    <td><span class="badge bg-<?= $statutBadge ?>"><?= ucfirst($cmd->getStatut()) ?></span></td>
                    <td><?= number_format($cmd->getTotal(), 2) ?> €</td>
                    <td class="small"><?= htmlspecialchars(substr($cmd->getAdresseLivraison(), 0, 30)) ?>...</td>
                    <td>
                        <?php if ($annulable): ?>
                            <a href="/ProjetMYTechno/index_.php?page=historique_commandes&annuler=<?= $cmd->getIdCommande() ?>"
                               class="btn btn-outline-danger btn-sm"
                               onclick="return confirm('Annuler cette commande ?')">
                                Annuler
                            </a>
                        <?php else: ?>
                            <span class="text-muted small">Non annulable</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>