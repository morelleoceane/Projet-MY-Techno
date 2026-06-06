<?php
/**
 * check_connection.php
 */

function checkClientConnecte(): void {
    if (!isset($_SESSION['client_id'])) {
        header('Location: /ProjetMYTechno/index_.php?page=connexion');
        exit();
    }
}

function checkAdminConnecte(): void {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /ProjetMYTechno/admin/index_.php?page=connexion_admin');
        exit();
    }
}