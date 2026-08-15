<?php
/**
 * Classe ClientDAO - Accès aux données Client (PostgreSQL)
 * Toutes les requêtes SQL sont ici uniquement.
 * INSERT/UPDATE/DELETE passent par des fonctions PL/pgSQL.
 * Fichier : ClientDAO.class.php
 */
class ClientDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Connection::getInstance();
    }

    /** Retourne tous les clients */
    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM client ORDER BY id_client");
        $rows = $stmt->fetchAll();
        $clients = [];
        foreach ($rows as $row) {
            $clients[] = new Client(
                (int)$row['id_client'], $row['nom_client'], $row['prenom_client'],
                $row['adresse_email'], $row['mot_de_passe'], $row['adresse_livraison'] ?? '',
                (bool)$row['est_banni']
            );
        }
        return $clients;
    }

    /** Retourne un client par son id */
    public function findById(int $id): ?Client {
        $stmt = $this->pdo->prepare("SELECT * FROM client WHERE id_client = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return new Client(
            (int)$row['id_client'], $row['nom_client'], $row['prenom_client'],
            $row['adresse_email'], $row['mot_de_passe'], $row['adresse_livraison'] ?? '',
            (bool)($row['est_banni'] ?? false), false
        );
    }

    /** Retourne un client par son email */
    public function findByEmail(string $email): ?Client {
        $stmt = $this->pdo->prepare("SELECT * FROM client WHERE adresse_email = :email");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return new Client(
            (int)$row['id_client'], $row['nom_client'], $row['prenom_client'],
            $row['adresse_email'], $row['mot_de_passe'], $row['adresse_livraison'] ?? '',
            (bool)$row['est_banni']
        );
    }

    /** Insère un client via fonction PL/pgSQL */
    public function insert(Client $client): void {
        $mdpHash = password_hash($client->getMotDePasse(), PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare(
            "SELECT inserer_client(:nom, :prenom, :email, :mdp, :adresse_livraison)"
        );
        $stmt->execute([
            ':nom'     => $client->getNomClient(),
            ':prenom'  => $client->getPrenomClient(),
            ':email'   => $client->getAdresseEmail(),
            ':mdp'     => $mdpHash,
            ':adresse_livraison' => $client->getAdresse() ?? '',
        ]);
    }

    /** Modifie un client via fonction PL/pgSQL */
    public function update(Client $client): void {
        $stmt = $this->pdo->prepare(
            "SELECT modifier_client(:id, :nom, :prenom, :adresse_livraison)"
        );
        $stmt->execute([
            ':id'      => $client->getIdClient(),
            ':nom'     => $client->getNomClient(),
            ':prenom'  => $client->getPrenomClient(),
            ':adresse_livraison' => $client->getAdresse(),
        ]);
    }

    /**
     * Supprime un client
     * ATTENTION : aucune fonction PL/pgSQL "supprimer_client" n'existe dans
     * la base (voir backups/plpgsql/) — seule bannir_client() est définie.
     * Il faudra ajouter une fonction plpgsql dédiée côté BD pour rendre
     * cette opération pleinement conforme (insert/update/delete → plpgsql).
     * En attendant, la requête reste correctement encapsulée dans la DAO.
     */
    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM client WHERE id_client = :id");
        $stmt->execute([':id' => $id]);
    }

    /**
     * Bannit un client via la fonction PL/pgSQL bannir_client()
     * CORRECTION : la version précédente faisait un UPDATE direct sur une
     * colonne "banni" qui n'existe pas (la vraie colonne est "est_banni"),
     * ce qui provoquait une erreur SQL. Elle passe désormais par la
     * fonction bannir_client() déjà définie dans backups/plpgsql/fonction_bannir.sql
     */
    public function bannir(int $id): void {
        $stmt = $this->pdo->prepare("SELECT bannir_client(:id)");
        $stmt->execute([':id' => $id]);
    }

    /** Vérifie les identifiants de connexion */
    public function verifierConnexion(string $email, string $mdp): ?Client {
        $client = $this->findByEmail($email);
        if ($client && password_verify($mdp, $client->getMotDePasse())) {
            return $client;
        }
        return null;
    }
}
