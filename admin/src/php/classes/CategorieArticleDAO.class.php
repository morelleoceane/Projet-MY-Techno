<?php
/**
 * Classe CategorieArticleDAO
 * Fichier : CategorieArticleDAO.class.php
 */
class CategorieArticleDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Connection::getInstance();
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM categorie_article ORDER BY nom_categorie");
        return array_map(
            fn($r) => new CategorieArticle((int)$r['id_categorie'], $r['nom_categorie']),
            $stmt->fetchAll()
        );
    }

    public function findById(int $id): ?CategorieArticle {
        $stmt = $this->pdo->prepare("SELECT * FROM categorie_article WHERE id_categorie=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? new CategorieArticle((int)$row['id_categorie'], $row['nom_categorie']) : null;
    }
}
