<?php
/**
 * verif_promo.php – Endpoint AJAX
 * Vérifie si un code promo est valide et retourne son taux
 */
header('Content-Type: application/json; charset=utf-8');
require_once dirname(__DIR__, 2) . '/php/utils/all_includes.php';

$code = trim($_GET['code'] ?? '');

if ($code === '') {
    echo json_encode(['valide' => false]);
    exit();
}

try {
    $pdo  = Connection::getInstance();
    $stmt = $pdo->prepare("SELECT taux_remise FROM promotion WHERE code_promo = :code");
    $stmt->execute([':code' => $code]);
    $row = $stmt->fetch();

    if ($row) {
        // Mémoriser dans la session pour utilisation au panier
        $_SESSION['promo_code'] = $code;
        $_SESSION['promo_taux'] = (int)$row['taux_remise'];
        echo json_encode(['valide' => true, 'taux' => (int)$row['taux_remise']]);
    } else {
        echo json_encode(['valide' => false]);
    }
} catch (Exception $e) {
    echo json_encode(['valide' => false, 'erreur' => $e->getMessage()]);
}
