"""
modules/article.py – Requêtes SQL liées à la table "article"
Toute la logique SQL concernant les articles est centralisée ici (modèle DAO) :
routes.py ne fait qu'appeler ces fonctions, jamais de SQL directement.
"""

from db import query_all, query_one


def get_articles_accueil(limite: int = 8) -> list:
    """Derniers articles actifs, pour la page d'accueil"""
    return query_all("""
        SELECT a.id_article, a.code_article, a.libelle, a.description,
               a.prix_unitaire, a.stock, a.photo_principale,
               a.marque, a.couleur, a.taille,
               c.nom_categorie
        FROM   article a
        LEFT JOIN categorie_article c ON c.id_categorie = a.id_categorie
        WHERE  a.est_actif = TRUE
        ORDER  BY a.id_article DESC
        LIMIT  %s
    """, (limite,))


def get_catalogue(cat_id: int = None, q: str = "", pmin: float = None,
                   pmax: float = None, tri: str = "prix_asc") -> list:
    """Articles filtrés/triés pour la page catalogue"""
    sql = """
        SELECT a.id_article, a.libelle, a.photo_principale,
               a.prix_unitaire, a.stock, a.code_article,
               a.marque, a.couleur, a.taille,
               c.nom_categorie, a.id_categorie
        FROM   article a
        LEFT JOIN categorie_article c ON c.id_categorie = a.id_categorie
        WHERE  a.est_actif = TRUE
    """
    params = []

    if cat_id:
        sql += " AND a.id_categorie = %s"
        params.append(cat_id)
    if q:
        sql += " AND (a.libelle ILIKE %s OR a.description ILIKE %s)"
        like = f"%{q}%"
        params.extend([like, like])
    if pmin is not None:
        sql += " AND a.prix_unitaire >= %s"
        params.append(pmin)
    if pmax is not None:
        sql += " AND a.prix_unitaire <= %s"
        params.append(pmax)

    ordre = {
        "prix_asc":  "a.prix_unitaire ASC",
        "prix_desc": "a.prix_unitaire DESC",
        "nom_asc":   "a.libelle ASC",
        "recent":    "a.id_article DESC",
    }.get(tri, "a.prix_unitaire ASC")
    sql += f" ORDER BY {ordre}"

    return query_all(sql, tuple(params))


def get_article_by_id(article_id: int):
    """Un article actif par son id (None si introuvable)"""
    return query_one("""
        SELECT a.id_article, a.code_article, a.libelle, a.description,
               a.prix_unitaire, a.stock, a.photo_principale,
               a.marque, a.couleur, a.taille,
               c.nom_categorie
        FROM   article a
        LEFT JOIN categorie_article c ON c.id_categorie = a.id_categorie
        WHERE  a.id_article = %s AND a.est_actif = TRUE
    """, (article_id,))


def get_avis_article(article_id: int) -> list:
    """Avis visibles pour un article donné"""
    return query_all("""
        SELECT av.note, av.commentaire, av.date_avis,
               cl.nom_client, cl.prenom_client
        FROM   avis_client av
        JOIN   client cl ON cl.id_client = av.id_client
        WHERE  av.id_article = %s AND av.est_visible = TRUE
        ORDER  BY av.date_avis DESC
    """, (article_id,))
