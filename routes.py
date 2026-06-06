"""
routes.py – Toutes les routes du projet TI2
"""

from flask import render_template, request, abort
from db import query_all, query_one


def register_routes(app):

    # ----------------------------------------------------------
    # ACCUEIL
    # ----------------------------------------------------------
    @app.route("/")
    def accueil():
        articles = query_all("""
            SELECT a.id_article, a.code_article, a.libelle, a.description,
                   a.prix_unitaire, a.stock, a.photo_principale,
                   a.marque, a.couleur, a.taille,
                   c.nom_categorie
            FROM   article a
            LEFT JOIN categorie_article c ON c.id_categorie = a.id_categorie
            WHERE  a.est_actif = TRUE
            ORDER  BY a.id_article DESC
            LIMIT  8
        """)
        return render_template("accueil.html", articles=articles)

    # ----------------------------------------------------------
    # CATALOGUE
    # ----------------------------------------------------------
    @app.route("/catalogue")
    def catalogue():
        cat_id = request.args.get("cat",  type=int)
        q      = request.args.get("q",    default="").strip()
        pmin   = request.args.get("pmin", default=None, type=float)
        pmax   = request.args.get("pmax", default=None, type=float)
        tri    = request.args.get("tri",  default="prix_asc")

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
            sql += " AND a.id_categorie = %s"; params.append(cat_id)
        if q:
            sql += " AND (a.libelle ILIKE %s OR a.description ILIKE %s)"
            like = f"%{q}%"; params.extend([like, like])
        if pmin is not None:
            sql += " AND a.prix_unitaire >= %s"; params.append(pmin)
        if pmax is not None:
            sql += " AND a.prix_unitaire <= %s"; params.append(pmax)

        ordre = {
            "prix_asc":  "a.prix_unitaire ASC",
            "prix_desc": "a.prix_unitaire DESC",
            "nom_asc":   "a.libelle ASC",
            "recent":    "a.id_article DESC",
        }.get(tri, "a.prix_unitaire ASC")
        sql += f" ORDER BY {ordre}"

        articles   = query_all(sql, tuple(params))
        categories = query_all("SELECT * FROM categorie_article ORDER BY nom_categorie")

        return render_template("catalogue.html",
                               articles=articles,
                               categories=categories,
                               cat_id=cat_id)

    # ----------------------------------------------------------
    # DÉTAIL ARTICLE
    # ----------------------------------------------------------
    @app.route("/article/<int:article_id>")
    def article_detail(article_id: int):
        article = query_one("""
            SELECT a.id_article, a.code_article, a.libelle, a.description,
                   a.prix_unitaire, a.stock, a.photo_principale,
                   a.marque, a.couleur, a.taille,
                   c.nom_categorie
            FROM   article a
            LEFT JOIN categorie_article c ON c.id_categorie = a.id_categorie
            WHERE  a.id_article = %s AND a.est_actif = TRUE
        """, (article_id,))
        if not article:
            abort(404)

        avis = query_all("""
            SELECT av.note, av.commentaire, av.date_avis,
                   cl.nom_client, cl.prenom_client
            FROM   avis_client av
            JOIN   client cl ON cl.id_client = av.id_client
            WHERE  av.id_article = %s AND av.est_visible = TRUE
            ORDER  BY av.date_avis DESC
        """, (article_id,))

        return render_template("article_detail.html", article=article, avis=avis)

    # ----------------------------------------------------------
    # LISTE DES CATÉGORIES
    # ----------------------------------------------------------
    @app.route("/categories")
    def liste_categories():
        cats = query_all("""
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
        return render_template("categories.html", categories=cats)