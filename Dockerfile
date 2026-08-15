# Utiliser Python 3.9 (version complète ici par sécurité d'exécution)

FROM python:3.11.9-slim



# Créer le dossier /app dans le container

WORKDIR /app



# Dans ce dossier /app, copier la suite :

# Copier le fichier requirements.txt de l'application vers le container

COPY requirements.txt .



# Installer les bibliothèques listées dans requirements.txt

RUN pip install --no-cache-dir -r requirements.txt



# Copier tout le contenu de l'url du projet vers /app

COPY . .



# Exposer le port 5000

#à exposer = créer dans la bulle isolée (le container) une passerelle vers le navigateur

# par le port 5000

EXPOSE 5000



# Lancer l'application

CMD ["python", "app.py"]

