<?php
/**
 * Classe Promotion - Entité métier
 * Fichier : Promotion.class.php
 */
class Promotion {
    private int    $id_promotion;
    private string $code_promo;
    private float  $taux_remise;   // numeric(5,2)
    private string $date_debut;    // date (Y-m-d)
    private string $date_fin;      // date (Y-m-d)
    private bool   $est_actif;     // boolean
    private int    $id_admin;

    public function __construct(
        int    $id         = 0,
        string $code       = '',
        float  $taux       = 0.0,
        string $date_debut = '',
        string $date_fin   = '',
        bool   $est_actif  = true,
        int    $id_admin   = 0
    ) {
        $this->id_promotion = $id;
        $this->code_promo   = $code;
        $this->taux_remise  = $taux;
        $this->date_debut   = $date_debut;
        $this->date_fin     = $date_fin;
        $this->est_actif    = $est_actif;
        $this->id_admin     = $id_admin;
    }

    // Getters
    public function getIdPromotion(): int    { return $this->id_promotion; }
    public function getCodePromo(): string   { return $this->code_promo; }
    public function getTauxRemise(): float   { return $this->taux_remise; }
    public function getDateDebut(): string   { return $this->date_debut; }
    public function getDateFin(): string     { return $this->date_fin; }
    public function getEstActif(): bool      { return $this->est_actif; }
    public function isActif(): bool          { return $this->est_actif; } // <-- méthode manquante ajoutée
    public function getIdAdmin(): int        { return $this->id_admin; }

    // Setters
    public function setIdPromotion(int $id): void       { $this->id_promotion = $id; }
    public function setCodePromo(string $c): void       { $this->code_promo = $c; }
    public function setTauxRemise(float $t): void       { $this->taux_remise = $t; }
    public function setDateDebut(string $d): void       { $this->date_debut = $d; }
    public function setDateFin(string $d): void         { $this->date_fin = $d; }
    public function setEstActif(bool $a): void          { $this->est_actif = $a; }
    public function setIdAdmin(int $id_admin): void     { $this->id_admin = $id_admin; }
}
