"""
app.py – Projet Flask TI2 – Version complète
=============================================
Fonctionnalités :
  - Routing multi-pages (accueil, catalogue, détail, catégories)
  - Connexion PostgreSQL via psycopg2 (même BD que le projet PHP)
  - Filtrage multicritères côté serveur
  - Context processors (catégories globales, date)
  - Gestion des erreurs 404 / 500
  - Variables d'environnement pour la config BD
"""

from flask import Flask, render_template, request, abort
import psycopg2
import psycopg2.extras
import os
from datetime import datetime

# ============================================================
# INITIALISATION
# ============================================================
app = Flask(__name__)
app.secret_key = os.environ.get("FLASK_SECRET", "ti2_secret_key_2026")

# ============================================================
# CONFIGURATION BASE DE DONNÉES
# ============================================================
DB_CONFIG = {
    "host":     os.environ.get("DB_HOST",   "localhost"),
    "port":     int(os.environ.get("DB_PORT", 5432)),
    "dbname":   os.environ.get("DB_NAME",   "baseprojet"),
    "user":     os.environ.get("DB_USER",   "postgres"),
    "password": os.environ.get("DB_PASS",   "oracle"),
}

# ============================================================
# HELPERS BASE DE DONNÉES
# ============================================================
def get_connection():
    return psycopg2.connect(**DB_CONFIG)

def get_cursor(conn):
    return conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor)

def query_all(sql: str, params: tuple = ()) -> list:
    try:
        conn = get_connection()
        cur  = get_cursor(conn)
        cur.execute(sql, params)
        rows = cur.fetchall()
        cur.close(); conn.close()
        return rows
    except Exception as e:
        print(f"[DB ERROR] query_all: {e}")
        return []

def query_one(sql: str, params: tuple = ()):
    try:
        conn = get_connection()
        cur  = get_cursor(conn)
        cur.execute(sql, params)
        row = cur.fetchone()
        cur.close(); conn.close()
        return row
    except Exception as e:
        print(f"[DB ERROR] query_one: {e}")
        return None

# ============================================================
# CONTEXT PROCESSORS – injectés dans TOUS les templates
# ============================================================
@app.context_processor
def inject_globals():
    g_categories = query_all(
        "SELECT id_categorie, nom_categorie FROM categorie_article ORDER BY nom_categorie"
    )
    return {"g_categories": g_categories, "now": datetime.now()}

# ============================================================
# ROUTES
# ============================================================

@app.route("/")
def accueil():
    articles = query_all("""
        SELECT a.id_article, a.libelle, a.photo_principale,
               a.prix_unitaire, a.marque, a.taille, a.couleur, a.stock,
               c.nom_categorie
        FROM   article a
        LEFT JOIN categorie_article c ON c.id_categorie = a.id_categorie
        WHERE  a.actif = TRUE
        ORDER  BY a.id_article DESC
        LIMIT  8
    """)
    return render_template("accueil.html", articles=articles)


@app.route("/catalogue")
def catalogue():
    cat_id = request.args.get("cat",   type=int)
    q      = request.args.get("q",     default="").strip()
    pmin   = request.args.get("pmin",  default=None, type=float)
    pmax   = request.args.get("pmax",  default=None, type=float)
    taille = request.args.get("taille",default="").strip()
    tri    = request.args.get("tri",   default="prix_asc")

    sql    = """
        SELECT a.id_article, a.libelle, a.photo_principale,
               a.prix_unitaire, a.marque, a.taille, a.couleur,
               a.stock, a.code_article, c.nom_categorie, a.id_categorie
        FROM   article a
        LEFT JOIN categorie_article c ON c.id_categorie = a.id_categorie
        WHERE  a.actif = TRUE
    """
    params = []

    if cat_id:
        sql += " AND a.id_categorie = %s"; params.append(cat_id)
    if q:
        sql += " AND (a.libelle ILIKE %s OR a.marque ILIKE %s OR a.couleur ILIKE %s)"
        like = f"%{q}%"; params.extend([like, like, like])
    if pmin is not None:
        sql += " AND a.prix_unitaire >= %s"; params.append(pmin)
    if pmax is not None:
        sql += " AND a.prix_unitaire <= %s"; params.append(pmax)
    if taille:
        sql += " AND a.taille = %s"; params.append(taille)

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


@app.route("/article/<int:article_id>")
def article_detail(article_id: int):
    article = query_one("""
        SELECT a.*, c.nom_categorie
        FROM   article a
        LEFT JOIN categorie_article c ON c.id_categorie = a.id_categorie
        WHERE  a.id_article = %s AND a.actif = TRUE
    """, (article_id,))
    if not article:
        abort(404)
    return render_template("article_detail.html", article=article)


@app.route("/categories")
def categories():
    cats = query_all("""
        SELECT c.id_categorie, c.nom_categorie,
               COUNT(a.id_article) AS nb_articles
        FROM   categorie_article c
        LEFT JOIN article a ON a.id_categorie = c.id_categorie AND a.actif = TRUE
        GROUP  BY c.id_categorie, c.nom_categorie
        ORDER  BY c.nom_categorie
    """)
    return render_template("categories.html", categories=cats)


# ============================================================
# GESTIONNAIRES D'ERREURS
# ============================================================
@app.errorhandler(404)
def page_not_found(e):
    return render_template("404.html"), 404

@app.errorhandler(500)
def server_error(e):
    return render_template("500.html"), 500


# ============================================================
# LANCEMENT
# ============================================================
if __name__ == "__main__":
    print("=" * 55)
    print("  ModeShopping Flask – Projet TI2")
    print(f"  BD : {DB_CONFIG['dbname']} @ {DB_CONFIG['host']}:{DB_CONFIG['port']}")
    print("  Routes : /  /catalogue  /article/<id>  /categories")
    print("  URL    : http://localhost:5000")
    print("=" * 55)
    app.run(debug=True, host="0.0.0.0", port=5000)
