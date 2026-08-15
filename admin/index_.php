<?php
/**
 * index_.php - Point d'entrée ADMINISTRATION
 * Les balises html/head/body/link/script ne figurent QUE ici.
 * CORRECTION : ob_start() pour permettre un vrai header('Location:...')
 * depuis les pages de contenu, même après le header/menu déjà "affichés".
 */
ob_start();


$page = $_GET['page'] ?? 'connexion_admin';

$pages_admin = [
    'connexion_admin', 'deconnexion_admin', 'accueil',
    'gestion_articles', 'gestion_commandes', 'gestion_clients',
    'gestion_promotions', 'page_404'
];

// Toutes les pages sauf connexion_admin nécessitent d'être admin
if ($page !== 'connexion_admin' && $page !== 'deconnexion_admin') {
    SecuriteAccess::checkAdminConnecte();
}

if (!in_array($page, $pages_admin)) {
    $page = 'page_404';
}

$fichier = __DIR__ . '/content/' . $page . '.php';
if (!file_exists($fichier)) {
    $fichier = __DIR__ . '/content/page_404.php';
}
?>
<?php require_once __DIR__ . '/src/php/utils/header.php'; ?>
<?php require_once __DIR__ . '/src/php/utils/admin_menu.php'; ?>

<main class="container my-4">
    <?php require_once $fichier; ?>
</main>

<?php require_once __DIR__ . '/src/php/utils/footer.php'; ?>
<?php ob_end_flush(); ?>
