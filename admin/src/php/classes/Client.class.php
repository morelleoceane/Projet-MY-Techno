<?php
/**
 * Classe Client - Entité métier
 * Fichier : Client.class.php
 */
class Client {
    private int    $id_client;
    private string $nom_client;
    private string $prenom_client;
    private string $adresse_email;
    private string $mot_de_passe;
    private string $adresse;
    private bool   $banni;
    private bool   $cgv_acceptees;

    public function __construct(
        int $id = 0, string $nom = '', string $prenom = '',
        string $email = '', string $mdp = '', string $adresse = '',
        bool $banni = false, bool $cgv = false
    ) {
        $this->id_client     = $id;
        $this->nom_client    = $nom;
        $this->prenom_client = $prenom;
        $this->adresse_email = $email;
        $this->mot_de_passe  = $mdp;
        $this->adresse       = $adresse;
        $this->banni         = $banni;
        $this->cgv_acceptees = $cgv;
    }

    // Getters
    public function getIdClient(): int         { return $this->id_client; }
    public function getNomClient(): string     { return $this->nom_client; }
    public function getPrenomClient(): string  { return $this->prenom_client; }
    public function getAdresseEmail(): string  { return $this->adresse_email; }
    public function getMotDePasse(): string    { return $this->mot_de_passe; }
    public function getAdresse(): string       { return $this->adresse; }
    public function isBanni(): bool            { return $this->banni; }
    public function isCgvAcceptees(): bool     { return $this->cgv_acceptees; }

    // Setters
    public function setNomClient(string $nom): void       { $this->nom_client = $nom; }
    public function setPrenomClient(string $p): void      { $this->prenom_client = $p; }
    public function setAdresseEmail(string $e): void      { $this->adresse_email = $e; }
    public function setMotDePasse(string $mdp): void      { $this->mot_de_passe = $mdp; }
    public function setAdresse(string $a): void           { $this->adresse = $a; }
    public function setBanni(bool $b): void               { $this->banni = $b; }
    public function setCgvAcceptees(bool $c): void        { $this->cgv_acceptees = $c; }
}
