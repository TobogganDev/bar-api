# Bar API

Ce projet consiste à mettre en place une API avec Symfony et API Platform pour gérer un bar. L'API doit permettre la gestion des commandes, des boissons et des utilisateurs (patron, barmans, serveurs et clients).

## Contributeurs
- Thomas DORET-GAÏSSET
- Quentin PACHEU

## Utiliser le projet

### Prérequis 

- PHP >= 8.3.1
- Composer
- Symfony CLI
- MySQL ou un autre SGBD compatible

### Installation

1 - Cloner le répo
```bash
git clone git@github.com:TobogganDev/bar-api.git
```

2 - Installer les dépendances
 ```bash
composer install
```

3 - Changer les variables d'environnements de la database dans le .env

4 - Générer les clés JWT
```bash
php bin/console lexik:jwt:generate-keypair
```

5 - Créez la base de données :
```bash
php bin/console doctrine:database:create
```

6 - Lancez le serveur Symfony :
```bash
symfony server:start
```

### Utilisation de Postman

1. Importez la collection Postman fournie dans le dépôt : `BAR-API_collection.json`.
   
2. Assurez-vous que les requêtes sont correctement configurées avec les en-têtes nécessaires (par exemple, les jetons d'authentification JWT).
  
3. Utilisez les requêtes pour interagir avec l'API selon les rôles et les fonctionnalités décrites.