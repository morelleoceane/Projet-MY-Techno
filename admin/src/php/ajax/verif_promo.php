<?php
/**
 * verif_promo.php – Endpoint AJAX
 * Vérifie si un code promo est valide et retourne son taux
 * CORRECTION : la requête SQL était écrite directement ici, en violation
 * de la règle "aucune requête SQL en dehors des classes DAO".
 * On passe désormais par PromotionDAO::findByCode().
 */
header('Content-Type: application/json; charset=utf-8');
require_once dirname(__DIR__, 2) . '/php/utils/all_includes.php';

$code = trim($_GET['code'] ?? '');

if ($code === '') {
    echo json_encode(['valide' => false]);
    exit();
}

try {
    $promoDAO = new PromotionDAO();
    $promo = $promoDAO->findByCode($code);

    if ($promo && $promo->isActif()) {
        // Mémoriser dans la session pour utilisation au panier
        $_SESSION['promo_code'] = $promo->getCodePromo();
        $_SESSION['promo_taux'] = (int)$promo->getTauxRemise();
        echo json_encode(['valide' => true, 'taux' => (int)$promo->getTauxRemise()]);
    } else {
        echo json_encode(['valide' => false]);
    }
} catch (Exception $e) {
    echo json_encode(['valide' => false, 'erreur' => $e->getMessage()]);
}
