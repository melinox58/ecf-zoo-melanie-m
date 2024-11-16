# ECF Zoo Arcadia MM
J'ai construit ce projet dans le cadre de mon évaluation en cours de formation DWWM

## Prérequis nécessaire :
- VSCode
- Symfony 7.0.*
- PHP 8.3.
- Composer
- MongoDB
    - Atlas
    - (sous) Docker
- SQL
    - environnement always data MariaDB
    - Wampp

## Installation du projet en local
- Cloner mon projet
    - ```git clone git@github.com:melinox58/ecf-zoo-melanie-m.git```

- Se positionner dans le dossier du projet
    - ```cd ecf-zoo-melanie-m```

- Créer et adapter un fichier .env.local pour contenir les variables d'environnement du projet(chemin bases de données)

- Importer le jeu de données : melinox_zooarcadia.sql, ........
    - ```mysql -u username -p database_name < path_to_mysql_file.sql```

- Importer les jeux de données MongoDB dans votre environnement de test
    - Utiliser Compass pour faire l'import
    - Ou Mongosh ```db.nom_de_la_collection.insertMany([{ document_1 },{ document_2 },{ document_3 },...]);```
    - Ou sur le terminal ```mongoimport --uri "mongodb://localhost:27017" --db nom_de_la_base --collection nom_de_la_collection --file /chemin/vers/votre/fichier.json --jsonArray```

- Executer la commande d'installation
    - ```composer install```

- Mettre a jour le projet
    - ```composer update```


## Test de l'application
- Ouvrir le terminal et faire la commande :
    - ```symfony server:start```

- Ouvrir un navigateur et mettre l'adresse : [localhost:](http://127.0.0.1:8000/)8000.

- Utiliser les identifiants que je vous ai communiqué dans le dossier d'ECF pour accéder à la partie admin de l'application.



