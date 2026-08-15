<?php
/**
 * index_.php - Point d'entrée PUBLIC
 * Centralise : autoload, session, connexion BD, inclusion dynamique des pages
 * Les balises html/head/body/link/script ne figurent QUE ici.
 * CORRECTION : ob_start() permet aux pages de contenu d'utiliser un vrai
 * header('Location:...') même après que le header/menu ont déjà été "affichés"
 * (rien n'est réellement envoyé au navigateur tant que le buffer n'est pas vidé).
 */
ob_start();

// Page demandée (défaut : accueil)
$page = $_GET['page'] ?? 'accueil';

// Pages autorisées (whitelist sécurité)
$pages_publiques = [
    'accueil', 'catalogue', 'article_detail', 'connexion', 'inscription',
    'deconnexion', 'panier', 'commande', 'mon_compte', 'historique_commandes',
    'cgv', 'page_404'
];

// Pages nécessitant connexion client
$pages_protegees = ['panier', 'commande', 'mon_compte', 'historique_commandes'];

if (in_array($page, $pages_protegees)) {
    SecuriteAccess::checkClientConnecte();
}

if (!in_array($page, $pages_publiques)) {
    $page = 'page_404';
}

$fichier = __DIR__ . '/content/' . $page . '.php';
if (!file_exists($fichier)) {
    $fichier = __DIR__ . '/content/page_404.php';
}
?>
<?php require_once __DIR__ . '/admin/src/php/utils/header.php'; ?>
<?php require_once __DIR__ . '/admin/src/php/utils/public_menu.php'; ?>

<main class="container my-4">
    <?php require_once $fichier; ?>
</main>

<?php require_once __DIR__ . '/admin/src/php/utils/footer.php'; ?>
<?php ob_end_flush(); ?>
