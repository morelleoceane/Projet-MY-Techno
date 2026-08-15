<?php
/**
 * update_article.php – Endpoint AJAX (Admin)
 * Met à jour un champ d'un article (stock, prix) via le tableau éditable admin
 * CORRECTION : la requête UPDATE était écrite directement ici (en dehors
 * d'une classe DAO) et ne passait par aucune fonction PL/pgSQL.
 * On passe désormais par ArticleDAO::update(), qui appelle modifier_article().
 */
header('Content-Type: application/json; charset=utf-8');
require_once dirname(__DIR__, 2) . '/php/utils/all_includes.php';

// Sécurité : admin uniquement
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode invalide']);
    exit();
}

$id_article = (int)($_POST['id_article'] ?? 0);
$champ      = $_POST['champ'] ?? '';
$valeur     = trim($_POST['valeur'] ?? '');

// Champs autorisés à la modification rapide
$champsAutorises = ['stock', 'prix_unitaire'];

if (!$id_article || !in_array($champ, $champsAutorises)) {
    echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
    exit();
}

// Validation selon le champ
if ($champ === 'stock' && !ctype_digit($valeur)) {
    echo json_encode(['success' => false, 'message' => 'Le stock doit être un entier']);
    exit();
}
if ($champ === 'prix_unitaire' && !is_numeric($valeur)) {
    echo json_encode(['success' => false, 'message' => 'Le prix doit être numérique']);
    exit();
}

try {
    $articleDAO = new ArticleDAO();
    $article    = $articleDAO->findById($id_article);

    if (!$article) {
        echo json_encode(['success' => false, 'message' => 'Article introuvable']);
        exit();
    }

    if ($champ === 'stock') {
        $article->setStock((int)$valeur);
    } else {
        $article->setPrix((float)$valeur);
    }

    $articleDAO->update($article);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
