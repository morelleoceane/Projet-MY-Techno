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
     * Récupère une promotion par son id (nécessaire pour delete() ci-dessous)
     */
    public function findById(int $id): ?Promotion {
        $stmt = $this->pdo->prepare("SELECT * FROM promotion WHERE id_promotion = :id");
        $stmt->execute([':id' => $id]);
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
     * CORRECTION : utilisait un INSERT direct au lieu de la fonction PL/pgSQL
     * "inserer_promotion" déjà définie dans backups/plpgsql/fonction_inserer_promotion.sql
     */
    public function insert(Promotion $p): void {
        $stmt = $this->pdo->prepare(
            "SELECT inserer_promotion(:code, :taux, :debut, :fin, :admin)"
        );
        $stmt->execute([
            ':code'  => $p->getCodePromo(),
            ':taux'  => $p->getTauxRemise(),
            ':debut' => $p->getDateDebut(),
            ':fin'   => $p->getDateFin(),
            ':admin' => $p->getIdAdmin()
        ]);
    }

    /**
     * Met à jour une promotion existante
     * CORRECTION : utilisait un UPDATE direct au lieu de la fonction PL/pgSQL
     * "modifier_promotion" déjà définie dans backups/plpgsql/fonction_modif_promotion.sql
     * (cette fonction ne permet pas de modifier le code_promo, seulement le taux,
     * les dates et le statut actif)
     */
    public function update(Promotion $p): void {
        $stmt = $this->pdo->prepare(
            "SELECT modifier_promotion(:id, :taux, :debut, :fin, :actif)"
        );
        $stmt->execute([
            ':id'    => $p->getIdPromotion(),
            ':taux'  => $p->getTauxRemise(),
            ':debut' => $p->getDateDebut(),
            ':fin'   => $p->getDateFin(),
            ':actif' => $p->isActif()
        ]);
    }

    /**
     * Désactive une promotion (soft-delete), cohérent avec le pattern déjà
     * utilisé pour les articles.
     * CORRECTION : un DELETE FROM direct violait à la fois la règle
     * "aucune opération sans fonction plpgsql" et supprimait définitivement
     * la donnée. Aucune fonction "supprimer_promotion" n'existe côté BD ;
     * on réutilise donc modifier_promotion avec est_actif = false.
     */
    public function delete(int $id): void {
        $promo = $this->findById($id);
        if ($promo === null) {
            return;
        }
        $stmt = $this->pdo->prepare(
            "SELECT modifier_promotion(:id, :taux, :debut, :fin, :actif)"
        );
        $stmt->execute([
            ':id'    => $id,
            ':taux'  => $promo->getTauxRemise(),
            ':debut' => $promo->getDateDebut(),
            ':fin'   => $promo->getDateFin(),
            ':actif' => false
        ]);
    }
}
