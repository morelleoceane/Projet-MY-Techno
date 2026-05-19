<?php
/**
 * historique_commandes.php - Annulation de commande
 */
require_once __DIR__ . '/../admin/src/php/utils/check_connection.php';
checkClientConnecte();

$commandeDAO = new CommandeDAO();

if (isset($_GET['annuler'])) {
    $idCmd   = (int)$_GET['annuler'];
    $commande = $commandeDAO->findById($idCmd);
    if ($commande && $commande->getIdClient() === $_SESSION['client_id']) {
        $ok = $commandeDAO->delete($idCmd);
        if ($ok) {
            $_SESSION['msg_success'] = "Commande #$idCmd annulée.";
        } else {
            $_SESSION['msg_erreur'] = "Cette commande ne peut plus être annulée (déjà expédiée).";
        }
    }
    header('Location: /index_.php?page=mon_compte');
    exit();
}
