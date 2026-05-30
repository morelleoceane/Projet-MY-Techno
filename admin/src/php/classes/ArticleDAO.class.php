<?php
/**
 * Classe ArticleDAO - Accès aux données Article (PostgreSQL)
 * Fichier : ArticleDAO.class.php
 */
class ArticleDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Connection::getInstance();
    }

    private function rowToArticle(array $row): Article {
        return new Article(
            (int)$row['id_article'], $row['code_article'], $row['libelle'],
            $row['photo_principale'] ?? '', (float)$row['prix_unitaire'],
            $row['taille'] ?? '', $row['couleur'] ?? '', $row['marque'] ?? '',
            (int)$row['stock'], (bool)$row['est_actif'], (int)$row['id_categorie']
        );
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM article ORDER BY id_article");
        return array_map([$this, 'rowToArticle'], $stmt->fetchAll());
    }

    public function findActifs(): array {
        $stmt = $this->pdo->query("SELECT * FROM article WHERE est_actif=TRUE ORDER BY id_article");
        return array_map([$this, 'rowToArticle'], $stmt->fetchAll());
    }

    public function findById(int $id): ?Article {
        $stmt = $this->pdo->prepare("SELECT * FROM article WHERE id_article = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->rowToArticle($row) : null;
    }

    /**
     * Recherche multicritères (filtrage visiteur)
     */
    public function findByCriteres(
        ?int $id_cat = null, ?string $taille = null,
        ?string $couleur = null, ?string $marque = null,
        ?float $prix_min = null, ?float $prix_max = null
    ): array {
        $sql = "SELECT * FROM article WHERE est_actif=TRUE";
        $params = [];
        if ($id_cat !== null) {
            $sql .= " AND id_categorie = :cat";
            $params[':cat'] = $id_cat;
        }
        if ($taille !== null && $taille !== '') {
            $sql .= " AND taille = :taille";
            $params[':taille'] = $taille;
        }
        if ($couleur !== null && $couleur !== '') {
            $sql .= " AND couleur ILIKE :couleur";
            $params[':couleur'] = $couleur;
        }
        if ($marque !== null && $marque !== '') {
            $sql .= " AND marque ILIKE :marque";
            $params[':marque'] = $marque;
        }
        if ($prix_min !== null) {
            $sql .= " AND prix_unitaire >= :pmin";
            $params[':pmin'] = $prix_min;
        }
        if ($prix_max !== null) {
            $sql .= " AND prix_unitaire <= :pmax";
            $params[':pmax'] = $prix_max;
        }
        $sql .= " ORDER BY prix_unitaire ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return array_map([$this, 'rowToArticle'], $stmt->fetchAll());
    }

    /** Insert via PL/pgSQL */
    public function insert(Article $a): void {
        $stmt = $this->pdo->prepare(
            "SELECT inserer_article(:code,:libelle,:photo,:prix,:taille,:couleur,:marque,:stock,:cat)"
        );
        $stmt->execute([
            ':code'    => $a->getCodeArticle(),
            ':libelle' => $a->getLibelle(),
            ':photo'   => $a->getPhoto(),
            ':prix'    => $a->getPrixUnitaire(),
            ':taille'  => $a->getTaille(),
            ':couleur' => $a->getCouleur(),
            ':marque'  => $a->getMarque(),
            ':stock'   => $a->getStock(),
            ':cat'     => $a->getIdCategorie(),
        ]);
    }

    /** Update via PL/pgSQL */
    public function update(Article $a): void {
        $stmt = $this->pdo->prepare(
            "SELECT modifier_article(:id,:libelle,:photo,:prix,:taille,:couleur,:marque,:stock,:actif)"
        );
        $stmt->execute([
            ':id'      => $a->getIdArticle(),
            ':libelle' => $a->getLibelle(),
            ':photo'   => $a->getPhoto(),
            ':prix'    => $a->getPrixUnitaire(),
            ':taille'  => $a->getTaille(),
            ':couleur' => $a->getCouleur(),
            ':marque'  => $a->getMarque(),
            ':stock'   => $a->getStock(),
            ':actif'   => $a->isActif() ? 'true' : 'false',
        ]);
    }

    /** Soft-delete via PL/pgSQL */
    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("SELECT supprimer_article(:id)");
        $stmt->execute([':id' => $id]);
    }
}
