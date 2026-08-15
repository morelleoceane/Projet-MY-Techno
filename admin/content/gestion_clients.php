<?php
/**
 * gestion_clients.php - Gestion clients (admin)
 */
$clientDAO = new ClientDAO();
$success = $erreur = '';

// Bannir
if (isset($_GET['bannir'])) {
    $clientDAO->bannir((int)$_GET['bannir']);
    $success = "Client banni.";
}
// Supprimer
if (isset($_GET['supprimer'])) {
    $clientDAO->delete((int)$_GET['supprimer']);
    $success = "Client supprimé.";
}

$clients = $clientDAO->findAll();
?>

<h2 class="mb-4"><i class="bi bi-people"></i> Gestion des clients</h2>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

<div class="table-responsive">
    <table class="table table-hover align-middle" id="tableau-clients">
        <thead class="table-dark">
            <tr>
                <th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th>
                <th>Banni</th><th>CGV</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client): ?>
            <tr class="<?= $client->isBanni() ? 'table-danger' : '' ?>">
                <td><?= $client->getIdClient() ?></td>
                <td><?= htmlspecialchars($client->getNomClient()) ?></td>
                <td><?= htmlspecialchars($client->getPrenomClient()) ?></td>
                <td><?= htmlspecialchars($client->getAdresseEmail()) ?></td>
                <td><?= $client->isBanni() ? '⛔ Oui' : '✅ Non' ?></td>
                <td><?= $client->isCgvAcceptees() ? '✅' : '❌' ?></td>
                <td>
                    <?php if (!$client->isBanni()): ?>
                    <a href="?page=gestion_clients&bannir=<?= $client->getIdClient() ?>"
                       class="btn btn-warning btn-sm"
                       data-confirm="Bannir ce client ?">Bannir</a>
                    <?php endif; ?>
                    <a href="?page=gestion_clients&supprimer=<?= $client->getIdClient() ?>"
                       class="btn btn-danger btn-sm"
                       data-confirm="Supprimer définitivement ce client ?">
                        <i class="bi bi-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
