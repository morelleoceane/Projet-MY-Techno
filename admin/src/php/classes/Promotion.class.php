<?php
/**
 * Classe Promotion - Entité métier
 * Fichier : Promotion.class.php
 */
class Promotion {
    private int    $id_promotion;
    private string $code_promo;
    private int    $taux_remise;
    private int    $id_admin;

    public function __construct(int $id=0, string $code='', int $taux=0, int $id_admin=0) {
        $this->id_promotion = $id;
        $this->code_promo   = $code;
        $this->taux_remise  = $taux;
        $this->id_admin     = $id_admin;
    }

    public function getIdPromotion(): int  { return $this->id_promotion; }
    public function getCodePromo(): string { return $this->code_promo; }
    public function getTauxRemise(): int   { return $this->taux_remise; }
    public function getIdAdmin(): int      { return $this->id_admin; }
    public function setCodePromo(string $c): void { $this->code_promo = $c; }
    public function setTauxRemise(int $t): void   { $this->taux_remise = $t; }
}
