<?php
/**
 * Classe Article - Entité métier
 * Fichier : Article.class.php
 */
class Article {
    private int    $id_article;
    private string $code_article;
    private string $libelle;
    private string $photo_principale;
    private float  $prix_unitaire;
    private string $taille;
    private string $couleur;
    private string $marque;
    private int    $stock;
    private bool   $actif;
    private int    $id_categorie;

    public function __construct(
        int $id = 0, string $code = '', string $libelle = '',
        string $photo = '', float $prix = 0.0, string $taille = '',
        string $couleur = '', string $marque = '', int $stock = 0,
        bool $actif = true, int $id_cat = 0
    ) {
        $this->id_article      = $id;
        $this->code_article    = $code;
        $this->libelle         = $libelle;
        $this->photo_principale = $photo;
        $this->prix_unitaire   = $prix;
        $this->taille          = $taille;
        $this->couleur         = $couleur;
        $this->marque          = $marque;
        $this->stock           = $stock;
        $this->actif           = $actif;
        $this->id_categorie    = $id_cat;
    }

    // Getters
    public function getIdArticle(): int        { return $this->id_article; }
    public function getCodeArticle(): string   { return $this->code_article; }
    public function getLibelle(): string       { return $this->libelle; }
    public function getPhoto(): string         { return $this->photo_principale; }
    public function getPrixUnitaire(): float   { return $this->prix_unitaire; }
    public function getTaille(): string        { return $this->taille; }
    public function getCouleur(): string       { return $this->couleur; }
    public function getMarque(): string        { return $this->marque; }
    public function getStock(): int            { return $this->stock; }
    public function isActif(): bool            { return $this->actif; }
    public function getIdCategorie(): int      { return $this->id_categorie; }

    // Setters
    public function setLibelle(string $l): void     { $this->libelle = $l; }
    public function setPhoto(string $p): void       { $this->photo_principale = $p; }
    public function setPrix(float $p): void         { $this->prix_unitaire = $p; }
    public function setTaille(string $t): void      { $this->taille = $t; }
    public function setCouleur(string $c): void     { $this->couleur = $c; }
    public function setMarque(string $m): void      { $this->marque = $m; }
    public function setStock(int $s): void          { $this->stock = $s; }
    public function setActif(bool $a): void         { $this->actif = $a; }
    public function setIdCategorie(int $id): void   { $this->id_categorie = $id; }
}
