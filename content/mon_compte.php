<?php
/**
 * mon_compte.php - Tableau de bord client
 */
require_once __DIR__ . '/../admin/src/php/utils/check_connection.php';
checkClientConnecte();

$clientDAO   = new ClientDAO();
$commandeDAO = new CommandeDAO();
$client = $clientDAO->findById($_SESSION['client_id']);

$success = '';
$erreur  = '';

// Mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profil'])) {
    $nom     = trim($_POST['nom'] ?? '');
    $prenom  = trim($_POST['prenom'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $adresse = trim($_POST['adresse_livraison'] ?? '');

    if (!$nom || !$prenom || !$email) {
        $erreur = "Nom, prénom et email sont obligatoires.";
    } else {
        $client->setNomClient($nom);
        $client->setPrenomClient($prenom);
        $client->setAdresseEmail($email);
        $client->setAdresse($adresse);
        try {
            $clientDAO->update($client);
            $_SESSION['client_nom']    = $nom;
            $_SESSION['client_prenom'] = $prenom;
            $success = "Profil mis à jour.";
            $client = $clientDAO->findById($_SESSION['client_id']);
        } catch (Exception $e) {
            $erreur = "Erreur : " . $e->getMessage(); // ← affiche le vrai message
        }
    }
}

$commandes = $commandeDAO->findByClient($client->getIdClient());
?>

<h2 class="mb-4"><i class="bi bi-person-circle"></i> Mon Compte</h2>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($erreur):  ?><div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>

<div class="row">
    <!-- Profil -->
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">
                <i class="bi bi-pencil"></i> Mes informations
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-2">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" id="nom" name="nom" class="form-control"
                               value="<?= htmlspecialchars($client->getNomClient()) ?>" required>
                    </div>
                    <div class="mb-2">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" id="prenom" name="prenom" class="form-control"
                               value="<?= htmlspecialchars($client->getPrenomClient()) ?>" required>
                    </div>
                    <div class="mb-2">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($client->getAdresseEmail()) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <textarea id="adresse" name="adresse" class="form-control" rows="2"><?= htmlspecialchars($client->getAdresse()) ?></textarea>
                    </div>
                    <button type="submit" name="update_profil" class="btn btn-dark btn-sm w-100">
                        Sauvegarder
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Historique commandes -->
    <div class="col-md-7 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">
                <i class="bi bi-clock-history"></i> Mes commandes
            </div>
            <div class="card-body">
                <?php if (empty($commandes)): ?>
                    <p class="text-muted">Aucune commande pour l'instant.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Adresse</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($commandes as $cmd): ?>
                                <?php
                                $statutBadge = match($cmd->getStatut()) {
                                    'en_attente' => 'warning',
                                    'validee', 'Validée' => 'primary',
                                    'expedie',  'Expédiée' => 'info',
                                    'livre',    'Livré'    => 'success',
                                    'Annulée',  'annulee'  => 'danger',
                                    default => 'secondary'
                                };
                                ?>
                                <tr>
                                    <td><?= $cmd->getIdCommande() ?></td>
                                    <td><?= htmlspecialchars($cmd->getDateCommande()) ?></td>
                                    <td>
                                    <span class="badge bg-<?= $statutBadge ?>">
                                        <?= ucfirst($cmd->getStatut()) ?>
                                    </span>
                                    </td>
                                    <td class="small"><?= htmlspecialchars(substr($cmd->getAdresseLivraison(), 0, 25)) ?>...</td>
                                    <td>
                                        <?php
                                        $statut = $cmd->getStatut();
                                        $annulable = !in_array($statut, ['expedie', 'Expédiée', 'livre', 'Livré', 'Annulée', 'annulee']);
                                        ?>
                                        <?php if ($annulable): ?>
                                            <a href="./index_.php?page=historique_commandes&annuler=<?= $cmd->getIdCommande() ?>"
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
            </div>
        </div>
    </div>
</div>