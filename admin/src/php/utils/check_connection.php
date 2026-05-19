<?php
/**
 * check_connection.php
 * Vérifie si l'utilisateur est connecté (client ou admin).
 * Redirige vers la page de connexion sinon.
 * Usage : require_once dans les pages protégées.
 */
function checkClientConnecte(): void {
    if (!isset($_SESSION['client_id'])) {
        header('Location: ../index_.php?page=connexion');
        exit();
    }
}

function checkAdminConnecte(): void {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: index_.php?page=connexion_admin');
        exit();
    }
}
