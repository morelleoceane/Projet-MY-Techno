"""
modules/categorie.py – Requêtes SQL liées à la table "categorie_article"
"""

from db import query_all


def get_all_categories() -> list:
    """Toutes les catégories (menu, formulaires, filtres)"""
    return query_all("SELECT * FROM categorie_article ORDER BY nom_categorie")


def get_categories_avec_nb_articles() -> list:
    """Catégories avec le nombre d'articles actifs pour chacune"""
    return query_all("""
        SELECT c.id_categorie,
               c.nom_categorie,
               c.description,
               COUNT(a.id_article) AS nb_articles
        FROM   categorie_article c
        LEFT JOIN article a
               ON a.id_categorie = c.id_categorie
              AND a.est_actif = TRUE
        GROUP  BY c.id_categorie, c.nom_categorie, c.description
        ORDER  BY c.nom_categorie
    """)
