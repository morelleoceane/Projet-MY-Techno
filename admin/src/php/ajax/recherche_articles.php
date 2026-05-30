<?php
/**
 * recherche_articles.php – Endpoint AJAX
 * Retourne les articles filtrés en JSON
 * Appelé par app.js (fetch API)
 */
header('Content-Type: application/json; charset=utf-8');

// Inclusion des ressources nécessaires (sans session ni menu)
require_once dirname(__DIR__, 2) . '/php/utils/all_includes.php';

$q = trim($_GET['q'] ?? '');

$pdo = Connection::getInstance();

$sql = "SELECT id_article, libelle, photo_principale, prix_unitaire,
               taille, couleur, marque, stock
        FROM article
        WHERE actif = TRUE";
$params = [];

if ($q !== '') {
    $sql .= " AND (libelle ILIKE :q OR marque ILIKE :q OR couleur ILIKE :q)";
    $params[':q'] = '%' . $q . '%';
}

$sql .= " ORDER BY libelle ASC LIMIT 50";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($articles, JSON_UNESCAPED_UNICODE);
