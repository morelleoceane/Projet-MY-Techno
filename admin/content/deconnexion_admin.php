<?php
/**
 * deconnexion_admin.php - Déconnexion administrateur
 */
unset($_SESSION['admin_id'], $_SESSION['admin_nom']);
session_destroy();
header('Location: ./index_.php?page=connexion_admin');
exit();
