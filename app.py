"""
app.py – Projet Flask TI2 – Initialisation & configuration
"""

from flask import Flask
import os
from datetime import datetime
from dotenv import load_dotenv
from db import query_all

load_dotenv()

# ============================================================
# INITIALISATION
# ============================================================
app = Flask(__name__)
app.secret_key = os.environ.get("FLASK_SECRET", "ti2_secret_key_2026")

# ============================================================
# CONTEXT PROCESSORS
# ============================================================
@app.context_processor
def inject_globals():
    g_categories = query_all(
        "SELECT id_categorie, nom_categorie FROM categorie_article ORDER BY nom_categorie"
    )
    return {"g_categories": g_categories, "now": datetime.now()}

# ============================================================
# GESTIONNAIRES D'ERREURS
# ============================================================
@app.errorhandler(404)
def page_not_found(_e):
    from flask import render_template
    return render_template("404.html"), 404

@app.errorhandler(500)
def server_error(_e):
    from flask import render_template
    return render_template("500.html"), 500

# ============================================================
# ENREGISTREMENT DES ROUTES
# ============================================================
from routes import register_routes
register_routes(app)

# ============================================================
# LANCEMENT
# ============================================================
if __name__ == "__main__":
    from db import DB_CONFIG
    print("=" * 55)
    print("  ModeShopping Flask – Projet TI2")
    print(f"  BD : {DB_CONFIG['dbname']} @ {DB_CONFIG['host']}:{DB_CONFIG['port']}")
    print("  Routes : /  /catalogue  /article/<id>  /categories")
    print("  URL    : http://localhost:5000")
    print("=" * 55)
    app.run(debug=True, host="0.0.0.0", port=5000)