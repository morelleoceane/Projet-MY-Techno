<?php
/**
 * all_includes.php
 * Point d'entrée unique pour :
 * - Démarrer la session
 * - Définir le chemin racine
 * - Définir l'URL de base
 * - Autocharger toutes les classes du dossier classes/
 * - Inclure la connexion DB
 * Ce fichier est inclus dans CHAQUE index.php (public et admin)
 */
session_start();

define('ROOT_PATH', dirname(__DIR__, 3)); // remonte jusqu'à /admin

define('BASE_URL', '/ProjetMYTechno/');

// ========================
// AUTOLOADING DES CLASSES
// ========================
spl_autoload_register(function (string $className): void {
    $file = ROOT_PATH . '/src/php/classes/' . $className . '.class.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// ========================
// CONNEXION BASE DE DONNÉES
// ========================
require_once ROOT_PATH . '/src/php/db/db_pg_connect.php';