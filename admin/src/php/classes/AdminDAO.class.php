<?php
/**
 * Classe AdminDAO - Accès aux données Admin (PostgreSQL)
 * Fichier : AdminDAO.class.php
 */
class AdminDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Connection::getInstance();
    }

    public function findByNom(string $nom): ?Admin {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM admin_site WHERE nom_admin=:nom AND compte_actif=TRUE"
        );
        $stmt->execute([':nom' => $nom]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return new Admin((int)$row['id_admin'], $row['nom_admin'], $row['mot_de_passe'], (bool)$row['compte_actif']);
    }

    public function verifierConnexion(string $nom, string $mdp): ?Admin {
        $admin = $this->findByNom($nom);
        if ($admin && password_verify($mdp, $admin->getMotDePasse())) {
            return $admin;
        }
        return null;
    }
}
