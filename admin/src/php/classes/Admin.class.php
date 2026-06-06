<?php
/**
 * Classe Admin - Entité métier
 * Fichier : Admin.class.php
 */
class Admin {
    private int    $id_admin;
    private string $nom_admin;
    private string $mot_de_passe;
    private bool   $compte_actif;

    public function __construct(
        int $id = 0, string $nom = '', string $mdp = '', bool $actif = true
    ) {
        $this->id_admin     = $id;
        $this->nom_admin    = $nom;
        $this->mot_de_passe = $mdp;
        $this->compte_actif = $actif;
    }

    public function getIdAdmin(): int       { return $this->id_admin; }
    public function getNomAdmin(): string   { return $this->nom_admin; }
    public function getMotDePasse(): string { return $this->mot_de_passe; }
    public function isCompteActif(): bool   { return $this->compte_actif; }
    public function setNomAdmin(string $n): void { $this->nom_admin = $n; }
}
