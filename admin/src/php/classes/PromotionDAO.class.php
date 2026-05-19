<?php
/**
 * Classe PromotionDAO - Accès aux données Promotion (PostgreSQL)
 * Fichier : PromotionDAO.class.php
 */
class PromotionDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Connection::getInstance();
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM promotion ORDER BY id_promotion");
        $rows = $stmt->fetchAll();
        return array_map(fn($r) => new Promotion(
            (int)$r['id_promotion'], $r['code_promo'], (int)$r['taux_remise'], (int)$r['id_admin']
        ), $rows);
    }

    public function findByCode(string $code): ?Promotion {
        $stmt = $this->pdo->prepare("SELECT * FROM promotion WHERE code_promo=:code");
        $stmt->execute([':code' => $code]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return new Promotion((int)$row['id_promotion'], $row['code_promo'], (int)$row['taux_remise'], (int)$row['id_admin']);
    }

    public function insert(Promotion $p): void {
        $stmt = $this->pdo->prepare("SELECT inserer_promotion(:code, :taux, :admin)");
        $stmt->execute([':code'=>$p->getCodePromo(), ':taux'=>$p->getTauxRemise(), ':admin'=>$p->getIdAdmin()]);
    }

    public function update(Promotion $p): void {
        $stmt = $this->pdo->prepare("SELECT modifier_promotion(:id, :code, :taux)");
        $stmt->execute([':id'=>$p->getIdPromotion(), ':code'=>$p->getCodePromo(), ':taux'=>$p->getTauxRemise()]);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("SELECT supprimer_promotion(:id)");
        $stmt->execute([':id' => $id]);
    }
}
