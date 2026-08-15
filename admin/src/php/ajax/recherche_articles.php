<?php
/**
 * recherche_articles.php – Endpoint AJAX
 * Retourne les articles filtrés en JSON
 * Appelé par app.js (fetch API)
 * CORRECTION : la requête SQL était construite directement ici, en violation
 * de la règle "aucune requête SQL en dehors des classes DAO".
 * On passe désormais par ArticleDAO::search().
 */
header('Content-Type: application/json; charset=utf-8');

// Inclusion des ressources nécessaires (sans session ni menu)
require_once dirname(__DIR__, 2) . '/php/utils/all_includes.php';

$q = trim($_GET['q'] ?? '');

$articleDAO = new ArticleDAO();
$articles   = $articleDAO->search($q);

// Reconversion en tableau simple pour le JSON attendu par app.js
$result = array_map(fn(Article $a) => [
    'id_article'       => $a->getIdArticle(),
    'libelle'          => $a->getLibelle(),
    'photo_principale' => $a->getPhoto(),
    'prix_unitaire'    => $a->getPrixUnitaire(),
    'taille'           => $a->getTaille(),
    'couleur'          => $a->getCouleur(),
    'marque'           => $a->getMarque(),
    'stock'            => $a->getStock(),
], $articles);

echo json_encode($result, JSON_UNESCAPED_UNICODE);
