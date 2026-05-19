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

    private function rowToCommande(array $row): Commande {
        return new Commande(
            (int)$row['id_commande'], $row['date_commande'] ?? '',
            (bool)$row['type_livraison'], $row['numero_suivi'] ?? '',
            $row['adresse_livraison'], $row['statut'], (int)$row['id_client']
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

    /** Crée une commande via PL/pgSQL, retourne l'id */
    public function insert(Commande $c): int {
        $stmt = $this->pdo->prepare(
            "SELECT inserer_commande(:adresse, :type, :id_client) AS id"
        );
        $stmt->execute([
            ':adresse'   => $c->getAdresseLivraison(),
            ':type'      => $c->isTypeLivraison() ? 'true' : 'false',
            ':id_client' => $c->getIdClient(),
        ]);
        return (int)$stmt->fetch()['id'];
    }

    /** Modifie le statut via PL/pgSQL */
    public function updateStatut(int $id, string $statut): void {
        $stmt = $this->pdo->prepare("SELECT modifier_statut_commande(:id, :statut)");
        $stmt->execute([':id' => $id, ':statut' => $statut]);
    }

    /** Suppression (annulation) via PL/pgSQL - uniquement si non expédié */
    public function delete(int $id): bool {
        $commande = $this->findById($id);
        if (!$commande || $commande->getStatut() === 'expedie') {
            return false;
        }
        $stmt = $this->pdo->prepare("SELECT supprimer_commande(:id)");
        $stmt->execute([':id' => $id]);
        return true;
    }

    /** Ajoute une ligne de commande via PL/pgSQL */
    public function insertLigne(int $id_commande, int $id_article, int $qte, float $prix): void {
        $stmt = $this->pdo->prepare(
            "SELECT inserer_ligne_commande(:cmd, :art, :qte, :prix)"
        );
        $stmt->execute([
            ':cmd'  => $id_commande,
            ':art'  => $id_article,
            ':qte'  => $qte,
            ':prix' => $prix,
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
