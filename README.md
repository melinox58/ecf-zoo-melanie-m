# ECF Zoo Arcadia MM
J'ai construit ce projet dans le cadre de mon évaluation en cours de formation DWWM.

# Réflexions initiales technologiques

- Objectifs

    Technologie principale : Symfony pour sa robustesse et ses nombreuses fonctionnalités adaptées aux projets web complexes.
    Base de données :
        MongoDB pour stocker les données flexibles liées aux rapports, statistiques, et avis des visiteurs (NoSQL, facile à manipuler et adapté pour des structures dynamiques).
        SQL pour les données relationnelles comme les utilisateurs, animaux, habitats, et services.
    Mailpit : Pour tester et simuler les envois d'e-mails de manière locale sans coût supplémentaire.
    Heroku : Plateforme de déploiement cloud pour héberger facilement l'application et assurer son accessibilité.

- Pourquoi ces choix ?

    Symfony : Framework complet avec une large communauté et une intégration facile avec MongoDB.
    MongoDB : Gère les données non relationnelles avec flexibilité et rapidité.
    Mailpit : Outil pratique pour tester les emails localement, sans configuration complexe.
    Heroku : Solution d’hébergement simple et efficace, avec intégration CI/CD.

## Prérequis nécessaire :
- VSCode
- Symfony 7.0.*
- PHP 8.3.6
- Composer
- MongoDB
    - Atlas
    - (sous) Docker
- SQL
    - environnement always data MariaDB
    - Wampp
- Mailpit
- Compte Heroku

## Installation du projet en local
- Cloner mon projet
    - ```git clone git@github.com:melinox58/ecf-zoo-melanie-m.git```

- Se positionner dans le dossier du projet
    - ```cd ecf-zoo-melanie-m```

- Créer et adapter un fichier .env.local pour contenir les variables d'environnement du projet(chemin bases de données)

- Importer le jeu de données : melinox_zooarcadia.sql.
    - ```mysql -u username -p database_name < path_to_mysql_file.sql```

- Importer les jeux de données MongoDB : Zoo_Arcadia.opinions.json, Zoo_Arcadia.schedules.json
    - Utiliser Compass pour faire l'import
    - Ou Mongosh ```db.nom_de_la_collection.insertMany([{ document_1 },{ document_2 },{ document_3 },...]);```
    - Ou sur le terminal ```mongoimport --uri "mongodb://localhost:27017" --db nom_de_la_base --collection nom_de_la_collection --file /chemin/vers/votre/fichier.json --jsonArray```

- Executer la commande d'installation des dépendances :
    - ```composer install```

- Mettre a jour les dépendances :
    - ```composer update```

- Installer Mailpit pour les essais mails :
    - Aller sur le site : https://github.com/axllent/mailpit/releases/tag/v1.21.3
    - dans les assets, choisir le téléchagement adapté a votre OS.
    - Mettre le mailpit.exe dans le fichier /bin de l'application.
    - Puis mettre a jour le .env avec : ```MAILER_DSN="smtp://localhost:1025"```

- Démarrer les services :
    - Lancer Mailpit sur le terminal :
        - Aller sur le fichier /bin : ```cd bin```
        - Puis ```./mailpit```
    - Wampp si utilisation en local
- Ouvrir un nouveau terminal et faire la commande :
    - ```symfony server:start```

## Déploiement
- Connexion Heroku CLI : 
    - heroku login (cliquer sur "se loger" ou entrer vos identifiants Heroku)
    - Création de l’application Heroku :
    ```heroku create ecf-zoo-melanie-m```
- Ajout des addons pour MySQL et MongoDB
    - JawsDB Maria : ``` heroku addons:create jawsdb-maria:kitefin```
- Configuration des variables d’environnement :
    - heroku config:set DATABASE_URL=...
    - APP_DEBUG
    - APP_ENV
    - APP_SECRET
    - DATABASE_URL
    - MONGODB_DB
    - MONGODB_URL
- Déploiement :
    - Ajouter un buildpack pour Symfony :
    ```heroku buildpacks:add heroku/php```
    - Push du projet :
    ```git push heroku main```

## Test de l'application
- Ouvrir un navigateur et mettre l'adresse : [localhost:](http://127.0.0.1:8000/)8000.

- Utiliser les identifiants que je vous ai communiqué dans le dossier d'ECF pour accéder à la partie admin de l'application.
