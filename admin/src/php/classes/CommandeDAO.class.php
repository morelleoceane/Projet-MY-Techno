<?php
/**
 * Classe CommandeDAO - Accès aux données Commande (PostgreSQL)
 * Fichier : CommandeDAO.class.php
 */
class CommandeDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Connection::getInstance();
    }

    /**
     * CORRECTION : la colonne "type_livraison" est de type TEXT en base
     * (voir backups/Dump/dump.sql) alors que la classe Commande la traite
     * comme un booléen (livraison express ou non). On convertit donc le
     * bool PHP en libellé texte ('Express' / 'Standard') pour la lecture.
     */
    private function rowToCommande(array $row): Commande {
        return new Commande(
            (int)$row['id_commande'], $row['date_commande'] ?? '',
            ($row['type_livraison'] ?? '') === 'Express', $row['numero_suivi'] ?? '',
            $row['adresse_livraison'], $row['statut'], (int)$row['id_client'],
            (float)($row['montant_total'] ?? 0.0)
        );
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM commande ORDER BY date_commande DESC");
        return array_map([$this, 'rowToCommande'], $stmt->fetchAll());
    }

    public function findByClient(int $id_client): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM commande WHERE id_client=:id ORDER BY date_commande DESC"
        );
        $stmt->execute([':id' => $id_client]);
        return array_map([$this, 'rowToCommande'], $stmt->fetchAll());
    }

    public function findById(int $id): ?Commande {
        $stmt = $this->pdo->prepare("SELECT * FROM commande WHERE id_commande=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->rowToCommande($row) : null;
    }

    /**
     * Crée une commande via PL/pgSQL, retourne l'id
     * CORRECTION : la fonction plpgsql attend un TEXTE pour p_type
     * (colonne type_livraison TEXT), pas un booléen brut.
     */
    public function insert(Commande $commande): int {
        $stmt = $this->pdo->prepare(
            "SELECT inserer_commande(:type, :adresse, :total, :cgv, :client) AS id"
        );
        $stmt->execute([
            ':type'    => $commande->isTypeLivraison() ? 'Express' : 'Standard',
            ':adresse' => $commande->getAdresseLivraison(),
            ':total'   => $commande->getTotal(),
            ':cgv'     => true,
            ':client'  => $commande->getIdClient(),
        ]);
        $row = $stmt->fetch();
        return (int)$row['id'];
    }

    /** Modifie le statut via PL/pgSQL */
    public function updateStatut(int $id, string $statut): void {
        $stmt = $this->pdo->prepare("SELECT modifier_statut_commande(:id, :statut)");
        $stmt->execute([':id' => $id, ':statut' => $statut]);
    }

    /** Suppression (annulation) via PL/pgSQL - uniquement si non expédié */
    public function delete(int $id): bool {
        $commande = $this->findById($id);
        if (!$commande) {
            return false;
        }
        $stmt = $this->pdo->prepare(
            "SELECT annuler_commande(:id, :client) AS result"
        );
        $stmt->execute([
            ':id'     => $id,
            ':client' => $commande->getIdClient(),
        ]);
        $row = $stmt->fetch();
        return (bool)$row['result'];
    }

    /** Ajoute une ligne de commande via PL/pgSQL */
    public function insertLigne(int $id_commande, int $id_article, int $qte, float $prix): void {
        $stmt = $this->pdo->prepare(
            "SELECT inserer_ligne_commande(:qte, :prix, :taille, :couleur, :cmd, :art)"
        );
        $stmt->execute([
            ':qte'    => $qte,
            ':prix'   => $prix,
            ':taille'  => '',   // non géré dans le panier, valeur vide par défaut
            ':couleur' => '',   // idem
            ':cmd'    => $id_commande,
            ':art'    => $id_article,
        ]);
    }

    /** Récupère les lignes d'une commande avec détails articles */
    public function findLignes(int $id_commande): array {
        $stmt = $this->pdo->prepare(
            "SELECT lc.*, a.libelle, a.photo_principale
             FROM ligne_commande lc
             JOIN article a ON a.id_article = lc.id_article
             WHERE lc.id_commande = :id"
        );
        $stmt->execute([':id' => $id_commande]);
        return $stmt->fetchAll();
    }
}
