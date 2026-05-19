INSERT INTO admin_site(nom_admin, email_admin, mot_de_passe)
VALUES ('Super Admin', 'admin@modeshop.be','shop2026');

INSERT INTO categorie_article(nom_categorie, description) VALUES
('Vêtements Homme', 'Vêtements pour homme'),
('Vêtements Femme', 'Vêtements pour femme'),
('Chaussures', 'Chaussures pour tous'),
('Accessoires', 'Accessoires de mode');

INSERT INTO article(code_article, libelle, description, prix_unitaire, stock, photo_principale, id_categorie) VALUES
('VH001', 'Jean Slim Homme', 'Jean coupe slim bleu', 49.99, 120, 'jean_slim.jpg', 1),
('VH002', 'T-shirt Coton Homme', 'T-shirt 100% coton', 19.99, 200, 'tshirt_coton.jpg', 1),
('VF001', 'Robe d’Été Femme', 'Robe légère pour l’été', 39.99, 80, 'robe_ete.jpg', 2);

INSERT INTO promotion(code_promo, taux_remise, date_debut, date_fin, est_actif, id_admin) VALUES
('BIENVENUE10', 10, '2024-01-01', '2024-12-31', TRUE, 1),
('ETE20', 20, '2024-06-01', '2024-08-31', TRUE, 1);

INSERT INTO client(nom_client, prenom_client, adresse_email, mot_de_passe)
VALUES ('LOBE', 'Oceane', 'oceane@efrei.fr','Morelle@2003');

