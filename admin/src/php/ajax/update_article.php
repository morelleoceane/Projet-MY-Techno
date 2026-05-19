<?php
/**
 * update_article.php – Endpoint AJAX (Admin)
 * Met à jour un champ d'un article (stock, prix)
 * via le tableau éditable admin
 */
header('Content-Type: application/json; charset=utf-8');
require_once dirname(__DIR__, 2) . '/utils/all_includes.php';
require_once dirname(__DIR__, 2) . '/utils/check_connection.php';

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
    $pdo = Connection::getInstance();
    // Utilise la requête directe (pas de fonction PL/pgSQL pour les mises à jour partielles)
    $stmt = $pdo->prepare("UPDATE article SET $champ = :val WHERE id_article = :id");
    $stmt->execute([':val' => $valeur, ':id' => $id_article]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
