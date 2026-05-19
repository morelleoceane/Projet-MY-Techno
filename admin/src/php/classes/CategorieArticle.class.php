<?php
/**
 * Classe CategorieArticle - Entité métier
 * Fichier : CategorieArticle.class.php
 */
class CategorieArticle {
    private int    $id_categorie;
    private string $nom_categorie;

    public function __construct(int $id = 0, string $nom = '') {
        $this->id_categorie  = $id;
        $this->nom_categorie = $nom;
    }

    public function getIdCategorie(): int      { return $this->id_categorie; }
    public function getNomCategorie(): string  { return $this->nom_categorie; }
    public function setNomCategorie(string $n): void { $this->nom_categorie = $n; }
}
