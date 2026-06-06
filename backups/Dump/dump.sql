CREATE EXTENSION IF NOT EXISTS "unaccent";


DROP TABLE IF EXISTS client_promotion CASCADE;
DROP TABLE IF EXISTS avis_client CASCADE;
DROP TABLE IF EXISTS ligne_commande CASCADE;
DROP TABLE IF EXISTS commande CASCADE;
DROP TABLE IF EXISTS promotion CASCADE;
DROP TABLE IF EXISTS admin_site CASCADE;
DROP TABLE IF EXISTS article CASCADE;
DROP TABLE IF EXISTS categorie_article CASCADE;
DROP TABLE IF EXISTS client CASCADE;

CREATE TABLE client (
    id_client       SERIAL PRIMARY KEY,
    nom_client      TEXT NOT NULL,
    prenom_client   TEXT NOT NULL,
    adresse_email   TEXT NOT NULL UNIQUE,
    mot_de_passe    TEXT NOT NULL,
    adresse_livraison TEXT,
    date_inscription DATE DEFAULT NOW(),
    est_banni       BOOLEAN DEFAULT FALSE
);

CREATE TABLE admin_site (
    id_admin        SERIAL PRIMARY KEY,
    nom_admin       TEXT NOT NULL,
    email_admin     TEXT NOT NULL UNIQUE,
    mot_de_passe    TEXT NOT NULL,
    compte_actif    BOOLEAN DEFAULT TRUE
);


CREATE TABLE categorie_article (
    id_categorie    SERIAL PRIMARY KEY,
    nom_categorie   TEXT NOT NULL UNIQUE,
    description     TEXT
);

CREATE TABLE article (
    id_article      SERIAL PRIMARY KEY,
    code_article    TEXT NOT NULL UNIQUE,
    libelle         TEXT NOT NULL,
    description     TEXT,
    prix_unitaire   NUMERIC(10,2) NOT NULL CHECK(prix_unitaire >= 0),
    stock           INTEGER DEFAULT 0 CHECK(stock >= 0),
    photo_principale TEXT,
    id_categorie    INTEGER REFERENCES categorie_article(id_categorie),
    est_actif       BOOLEAN DEFAULT TRUE
);
CREATE TABLE commande (
    id_commande         SERIAL PRIMARY KEY,
    date_commande       DATE DEFAULT NOW(),
    statut              TEXT DEFAULT 'En attente'
                        CHECK(statut IN ('En attente','Validée','Expédiée','Annulée','Remboursée')),
    type_livraison      TEXT NOT NULL,
    numero_suivi        TEXT,
    adresse_livraison   TEXT NOT NULL,
    montant_total       NUMERIC(10,2) NOT NULL,
    cgv_acceptees       BOOLEAN NOT NULL DEFAULT FALSE,
    id_client           INTEGER NOT NULL REFERENCES client(id_client)
);

CREATE TABLE ligne_commande (
    id_ligne            SERIAL PRIMARY KEY,
    quantite            INTEGER NOT NULL CHECK(quantite > 0),
    prix_unitaire_achat NUMERIC(10,2) NOT NULL,
    taille              TEXT,
    couleur             TEXT,
    id_commande         INTEGER NOT NULL REFERENCES commande(id_commande) ON DELETE CASCADE,
    id_article          INTEGER NOT NULL REFERENCES article(id_article)
);

CREATE TABLE promotion (
    id_promotion    SERIAL PRIMARY KEY,
    code_promo      TEXT NOT NULL UNIQUE,
    taux_remise     NUMERIC(5,2) NOT NULL CHECK(taux_remise BETWEEN 0 AND 100),
    date_debut      DATE NOT NULL,
    date_fin        DATE NOT NULL,
    est_actif       BOOLEAN DEFAULT TRUE,
    id_admin        INTEGER REFERENCES admin_site(id_admin)
);

CREATE TABLE client_promotion (
    id_client_promo SERIAL PRIMARY KEY,
    utilise         BOOLEAN DEFAULT FALSE,
    date_utilisation DATE,
    id_client       INTEGER NOT NULL REFERENCES client(id_client),
    id_promotion    INTEGER NOT NULL REFERENCES promotion(id_promotion)
);

CREATE TABLE avis_client (
    id_avis         SERIAL PRIMARY KEY,
    note            INTEGER NOT NULL CHECK(note BETWEEN 1 AND 5),
    commentaire     TEXT,
    date_avis       DATE DEFAULT NOW(),
    est_visible     BOOLEAN DEFAULT TRUE,
    id_client       INTEGER NOT NULL REFERENCES client(id_client),
    id_article      INTEGER NOT NULL REFERENCES article(id_article)
);

