<?php
/**
 * panier_count.php – Endpoint AJAX
 * Retourne le nombre d'articles dans le panier (session)
 */
header('Content-Type: application/json; charset=utf-8');
require_once dirname(__DIR__, 2) . '/utils/all_includes.php';

$count = isset($_SESSION['panier']) ? count($_SESSION['panier']) : 0;
echo json_encode(['count' => $count]);
