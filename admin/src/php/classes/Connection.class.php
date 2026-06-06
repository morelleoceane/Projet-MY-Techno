<?php
/**
 * Classe Connection - Singleton PDO PostgreSQL
 * Fichier : Connection.class.php
 */

class Connection {
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            // Initialisation des variables avant le require
            // pour éviter les avertissements "Undefined variable"
            $dsn = $user = $pass = '';
            require __DIR__ . '/../db/db_pg_connect.php';
            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die('Erreur de connexion à la base de données : ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}