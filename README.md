# Gestion des Commandes - Application PHP/MySQL

Application web complète de gestion des commandes clients avec PHP et MySQL.

## Fonctionnalités

- **Système d'authentification** : Login/Logout avec sessions PHP
- **Gestion des clients** : Ajouter, modifier, supprimer, lister et rechercher des clients
- **Gestion des produits** : Ajouter, modifier, supprimer, lister et rechercher des produits avec gestion du stock
- **Gestion des commandes** : Créer des commandes avec vérification du stock, génération automatique de fichiers texte
- **Dashboard** : Vue d'ensemble avec statistiques et alertes de stock

## Identifiants de connexion

- **Login** : admin
- **Mot de passe** : admin2026

## Installation

### 1. Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache, Nginx, ou XAMPP/WAMP/MAMP)

### 2. Configuration de la base de données

1. Créer une base de données nommée `gestion_commandes`
2. Importer le fichier `database.sql` :

```bash
mysql -u root -p gestion_commandes < database.sql
```

Ou utiliser phpMyAdmin pour importer le fichier SQL.

### 3. Configuration de la connexion

Modifier le fichier `base.php` si nécessaire pour adapter les paramètres de connexion :

```php
$host = 'localhost';
$dbname = 'gestion_commandes';
$username = 'root';  // Votre utilisateur MySQL
$password = '';      // Votre mot de passe MySQL
```

### 4. Déploiement

Copier tous les fichiers dans le répertoire racine de votre serveur web (ex: `htdocs` pour XAMPP).

## Structure des fichiers

```
gestion_commandes/
├── base.php              # Connexion à la base de données
├── database.sql          # Script de création de la base de données
├── index.php             # Dashboard
├── login.php             # Page de connexion
├── logout.php            # Déconnexion
├── style.css             # Feuille de styles
├── README.md             # Ce fichier
├── ajouter_client.php    # Ajouter un client
├── liste_clients.php     # Liste des clients
├── modifier_client.php   # Modifier un client
├── ajouter_produit.php   # Ajouter un produit
├── liste_produits.php    # Liste des produits
├── modifier_produit.php  # Modifier un produit
├── ajouter_commande.php  # Ajouter une commande
├── liste_commandes.php   # Liste des commandes
├── modifier_commande.php # Modifier une commande
└── Facture/              # Dossier des fichiers de commande générés
```

## Structure de la base de données

### Table `users`
- id (INT, PK, AI)
- login (VARCHAR)
- password (VARCHAR)

### Table `clients`
- id (INT, PK, AI)
- code_client (VARCHAR, UNIQUE)
- nom (VARCHAR)
- prenom (VARCHAR)
- email (VARCHAR, UNIQUE)
- telephone (VARCHAR)

### Table `produits`
- id (INT, PK, AI)
- nom (VARCHAR)
- description (TEXT)
- prix (DECIMAL)
- stock (INT)

### Table `commandes`
- id (INT, PK, AI)
- client_id (INT, FK)
- produit_id (INT, FK)
- quantite (INT)
- date_commande (DATETIME)
- montant_total (DECIMAL)

## Fonctionnement des commandes

1. **Création d'une commande** :
   - Sélectionner un client
   - Sélectionner un produit
   - Indiquer la quantité
   - Le système vérifie que le stock est suffisant
   - Le stock est automatiquement décrémenté
   - Un fichier texte est généré dans le dossier `Facture/`

2. **Modification d'une commande** :
   - Le stock de l'ancien produit est remis
   - Le stock du nouveau produit est décrémenté

3. **Suppression d'une commande** :
   - Le stock du produit est remis automatiquement

## Génération des fichiers de commande

Les fichiers sont générés dans le dossier `Facture/` avec le format : `commande_YYYY-MM-DD_HH-mm-ss.txt`

Chaque fichier contient :
- Date et numéro de commande
- Informations du client (code, nom, prénom, email)
- Détails du produit commandé
- Prix unitaire et quantité
- Montant total

## Sécurité

- Protection contre les injections SQL avec PDO et requêtes préparées
- Hashage des mots de passe (bcrypt)
- Protection des pages par sessions PHP
- Échappement des données affichées (XSS protection)

## Technologies utilisées

- PHP 7.4+
- MySQL 5.7+
- PDO pour les interactions avec la base de données
- Font Awesome pour les icônes
- CSS personnalisé (responsive)

## Auteur

MELC-98.
