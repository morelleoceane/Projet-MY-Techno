<?php
class PromotionDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Connection::getInstance();
    }

    /**
     * Récupère toutes les promotions
     */
    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM promotion ORDER BY id_promotion");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($r) => new Promotion(
            (int)$r['id_promotion'],
            $r['code_promo'],
            (float)$r['taux_remise'],
            $r['date_debut'],
            $r['date_fin'],
            (bool)$r['est_actif'],
            (int)$r['id_admin']
        ), $rows);
    }

    /**
     * Recherche une promotion par son code
     */
    public function findByCode(string $code): ?Promotion {
        $stmt = $this->pdo->prepare("SELECT * FROM promotion WHERE code_promo = :code");
        $stmt->execute([':code' => $code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Promotion(
            (int)$row['id_promotion'],
            $row['code_promo'],
            (float)$row['taux_remise'],
            $row['date_debut'],
            $row['date_fin'],
            (bool)$row['est_actif'],
            (int)$row['id_admin']
        ) : null;
    }

    /**
     * Insère une nouvelle promotion
     */
    public function insert(Promotion $p): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO promotion 
                (code_promo, taux_remise, date_debut, date_fin, est_actif, id_admin)
            VALUES 
                (:code, :taux, :debut, :fin, :actif, :admin)
        ");

        $stmt->execute([
            ':code'  => $p->getCodePromo(),
            ':taux'  => $p->getTauxRemise(),
            ':debut' => $p->getDateDebut(),
            ':fin'   => $p->getDateFin(),
            ':actif' => $p->isActif(),
            ':admin' => $p->getIdAdmin()
        ]);
    }

    /**
     * Met à jour une promotion existante
     */
    public function update(Promotion $p): void {
        $stmt = $this->pdo->prepare("
            UPDATE promotion
            SET code_promo = :code,
                taux_remise = :taux,
                date_debut = :debut,
                date_fin = :fin,
                est_actif = :actif
            WHERE id_promotion = :id
        ");

        $stmt->execute([
            ':id'    => $p->getIdPromotion(),
            ':code'  => $p->getCodePromo(),
            ':taux'  => $p->getTauxRemise(),
            ':debut' => $p->getDateDebut(),
            ':fin'   => $p->getDateFin(),
            ':actif' => $p->isActif()
        ]);
    }

    /**
     * Supprime une promotion
     */
    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM promotion WHERE id_promotion = :id");
        $stmt->execute([':id' => $id]);
    }
}
