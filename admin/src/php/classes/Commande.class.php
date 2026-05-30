<?php
/**
 * Classe Commande - Entité métier
 * Fichier : Commande.class.php
 */
class Commande {
    private int    $id_commande;
    private string $date_commande;
    private bool   $type_livraison;
    private string $numero_suivi;
    private string $adresse_livraison;
    private string $statut;
    private int    $id_client;

    public function __construct(
        int $id = 0, string $date = '', bool $type = false,
        string $suivi = '', string $adresse = '',
        string $statut = 'en_attente', int $id_client = 0,
        float $montant_total = 0.0
    ) {
        $this->id_commande       = $id;
        $this->date_commande     = $date;
        $this->type_livraison    = $type;
        $this->numero_suivi      = $suivi;
        $this->adresse_livraison = $adresse;
        $this->statut            = $statut;
        $this->id_client         = $id_client;
        $this->montant_total = $montant_total;
    }

    public function getTotal(): float { return $this->montant_total; }
    public function getIdCommande(): int        { return $this->id_commande; }
    public function getDateCommande(): string   { return $this->date_commande; }
    public function isTypeLivraison(): bool     { return $this->type_livraison; }
    public function getNumeroSuivi(): string    { return $this->numero_suivi; }
    public function getAdresseLivraison(): string { return $this->adresse_livraison; }
    public function getStatut(): string         { return $this->statut; }
    public function getIdClient(): int          { return $this->id_client; }

    public function setStatut(string $s): void        { $this->statut = $s; }
    public function setNumeroSuivi(string $n): void   { $this->numero_suivi = $n; }
}
