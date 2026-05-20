from app import app


@app.route('/accueil')
def accueil():
    return "Page d'accueil"

@app.route('/produits')
def produits():
    return "Page des produits"