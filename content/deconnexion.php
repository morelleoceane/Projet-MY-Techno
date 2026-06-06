<?php
/**
 * deconnexion.php - Déconnexion client
 */
session_destroy();
header('Location: ./index_.php?page=accueil');
exit();
