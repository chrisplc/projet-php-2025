# 🎭 Spektacles - Plateforme de Réservation de Spectacles

**Spektacles** est une application web moderne développée avec Symfony 7.3 permettant la gestion et la réservation de spectacles en ligne. L'application offre une interface utilisateur intuitive pour les clients et un panneau d'administration complet pour la gestion des spectacles, utilisateurs et réservations.

## 📋 Table des matières

- [Fonctionnalités](#-fonctionnalités)
- [Technologies utilisées](#-technologies-utilisées)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Structure du projet](#-structure-du-projet)
- [Arborescence détaillée](#-arborescence-détaillée)
- [Utilisation](#-utilisation)
- [Commandes disponibles](#-commandes-disponibles)
- [Sécurité](#-sécurité)
- [Contributeurs](#-contributeurs)

## ✨ Fonctionnalités

### Pour les utilisateurs
- ✅ **Inscription et authentification** : Création de compte et connexion sécurisée
- ✅ **Catalogue de spectacles** : Affichage de tous les spectacles disponibles avec leurs détails
- ✅ **Réservation en ligne** : Sélection du nombre de places et calcul automatique du prix total
- ✅ **Confirmation de réservation** : Page récapitulative avec tous les détails de la réservation
- ✅ **Interface responsive** : Design moderne et adaptatif avec Tailwind CSS

### Pour les administrateurs
- ✅ **Dashboard EasyAdmin** : Interface d'administration complète et intuitive
- ✅ **Gestion des utilisateurs** : CRUD complet pour les comptes utilisateurs
- ✅ **Gestion des spectacles** : Ajout, modification et suppression de spectacles
- ✅ **Gestion des réservations** : Visualisation et gestion de toutes les réservations
- ✅ **Statistiques** : Vue d'ensemble des réservations par spectacle avec chiffres clés
- ✅ **Tableau de bord** : Vue d'ensemble avec statistiques et alertes

## 🛠 Technologies utilisées

### Backend
- **PHP 8.2+** : Langage de programmation
- **Symfony 7.3** : Framework PHP moderne
- **Doctrine ORM 3.5** : Gestion de la base de données
- **EasyAdmin Bundle** : Interface d'administration
- **FakerPHP** : Génération de données de test

### Frontend
- **Twig** : Moteur de template
- **Tailwind CSS 3.4** : Framework CSS utilitaire (via CDN)
- **Phosphor Icons** : Bibliothèque d'icônes
- **Webpack Encore** : Build des assets

### Base de données
- **MySQL/MariaDB** : Système de gestion de base de données
- **Doctrine Migrations** : Gestion des migrations de schéma

## 📦 Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- **PHP 8.2 ou supérieur** avec les extensions suivantes :
  - `ext-ctype`
  - `ext-iconv`
  - `ext-pdo`
  - `ext-pdo_mysql`
- **Composer** : Gestionnaire de dépendances PHP
- **Node.js 18+ et npm** : Pour la compilation des assets
- **MySQL/MariaDB** : Base de données
- **Symfony CLI** (optionnel mais recommandé)

## 🚀 Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/chrisplc/projet-php-2025.git
cd projet-php-2025
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Installer les dépendances JavaScript

```bash
npm install
```

### 4. Configurer l'environnement

Copiez le fichier `.env` et configurez vos variables d'environnement :

```bash
cp .env .env.local
```

Éditez `.env.local` et configurez votre base de données :

```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/spektacles?serverVersion=8.0&charset=utf8mb4"
```

### 5. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 6. Générer les données de test (optionnel)

```bash
php bin/console app:generate-fixtures
```

### 7. Créer un compte administrateur

```bash
php bin/console app:create-admin
```

Par défaut, cela crée un compte admin avec :
- **Email** : `admin@test.com`
- **Mot de passe** : `admin`

### 8. Compiler les assets

```bash
npm run build
```

Ou en mode développement avec watch :

```bash
npm run watch
```

### 9. Lancer le serveur de développement

```bash
symfony server:start
```

Ou avec PHP intégré :

```bash
php -S localhost:8000 -t public
```

L'application sera accessible à l'adresse : **http://localhost:8000**

## ⚙️ Configuration

### Configuration de la sécurité

Le fichier `config/packages/security.yaml` définit :

- **Provider utilisateur** : Entité `Utilisateur` avec email comme identifiant
- **Firewall principal** : Authentification par formulaire
- **Contrôle d'accès** :
  - `/reservation/*` : Nécessite `ROLE_USER`
  - `/admin/*` : Nécessite `ROLE_ADMIN`

### Configuration EasyAdmin

Le dashboard d'administration est accessible à `/admin` et inclut :

- **Utilisateurs** : Gestion complète des comptes
- **Spectacles** : Gestion du catalogue
- **Réservations** : Suivi des réservations
- **Statistiques** : Vue d'ensemble des performances

## 📁 Structure du projet

```
projet-php-2025/
├── assets/                 # Assets frontend (JS, CSS)
│   ├── app.js
│   └── styles/
│       └── app.css
├── bin/                    # Scripts exécutables
│   └── console            # Console Symfony
├── config/                 # Configuration Symfony
│   ├── packages/          # Configuration des bundles
│   └── routes/            # Configuration des routes
├── migrations/             # Migrations Doctrine
├── public/                 # Point d'entrée web
│   ├── index.php
│   ├── fonts/             # Polices personnalisées
│   └── images/            # Images statiques
├── src/                    # Code source de l'application
│   ├── Command/           # Commandes console
│   ├── Controller/        # Contrôleurs
│   ├── Entity/            # Entités Doctrine
│   └── Repository/        # Repositories Doctrine
├── templates/              # Templates Twig
│   ├── admin/             # Templates admin
│   ├── home/              # Templates accueil
│   ├── reservation/       # Templates réservation
│   └── security/          # Templates authentification
├── translations/           # Fichiers de traduction
├── var/                    # Fichiers temporaires et cache
└── vendor/                 # Dépendances Composer
```

## 🌳 Arborescence détaillée

### `/src` - Code source principal

```
src/
├── Command/
│   ├── CreateAdminCommand.php          # Création d'un compte admin
│   ├── GenerateFixturesCommand.php     # Génération de données de test
│   └── ListUsersCommand.php            # Liste des utilisateurs
│
├── Controller/
│   ├── Admin/
│   │   ├── DashboardController.php     # Dashboard admin EasyAdmin
│   │   ├── ReservationCrudController.php  # CRUD réservations
│   │   ├── SpectacleCrudController.php    # CRUD spectacles
│   │   └── UtilisateurCrudController.php  # CRUD utilisateurs
│   │
│   ├── HomeController.php              # Page d'accueil
│   ├── ReservationController.php       # Gestion des réservations
│   └── SecurityController.php          # Authentification
│
├── Entity/
│   ├── Reservation.php                 # Entité réservation
│   ├── Spectacle.php                   # Entité spectacle
│   └── Utilisateur.php                 # Entité utilisateur
│
└── Repository/
    ├── ReservationRepository.php       # Repository réservations
    ├── SpectacleRepository.php         # Repository spectacles
    └── UtilisateurRepository.php       # Repository utilisateurs
```

### `/templates` - Templates Twig

```
templates/
├── base.html.twig                      # Template de base
├── home/
│   └── index.html.twig                 # Page d'accueil
├── reservation/
│   ├── reserver.html.twig              # Formulaire de réservation
│   └── confirmation.html.twig          # Confirmation de réservation
├── security/
│   ├── login.html.twig                 # Page de connexion
│   └── register.html.twig              # Page d'inscription
└── admin/
    └── (templates EasyAdmin personnalisés)
```

### `/config` - Configuration

```
config/
├── packages/
│   ├── doctrine.yaml                   # Configuration Doctrine ORM
│   ├── security.yaml                   # Configuration sécurité
│   ├── twig.yaml                       # Configuration Twig
│   └── webpack_encore.yaml             # Configuration Webpack
└── routes/
    ├── easyadmin.yaml                  # Routes EasyAdmin
    └── security.yaml                   # Routes sécurité
```

## 🎯 Utilisation

### Accès utilisateur

1. **Inscription** : Créez un compte via `/register`
2. **Connexion** : Connectez-vous via `/login`
3. **Parcourir les spectacles** : Consultez le catalogue sur la page d'accueil
4. **Réserver** : Cliquez sur "Réserver" pour un spectacle et choisissez le nombre de places
5. **Confirmation** : Visualisez le récapitulatif de votre réservation

### Accès administrateur

1. **Connexion admin** : Connectez-vous avec `admin@test.com` / `admin`
2. **Dashboard** : Accédez à `/admin` pour le tableau de bord
3. **Gestion** : Utilisez les menus pour gérer utilisateurs, spectacles et réservations
4. **Statistiques** : Consultez les statistiques détaillées par spectacle

## 🔧 Commandes disponibles

### Commandes Symfony standard

```bash
# Vider le cache
php bin/console cache:clear

# Créer une migration
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Créer une entité
php bin/console make:entity

# Créer un contrôleur
php bin/console make:controller
```

### Commandes personnalisées

```bash
# Créer un compte administrateur
php bin/console app:create-admin

# Générer des données de test (utilisateurs et spectacles)
php bin/console app:generate-fixtures

# Lister tous les utilisateurs
php bin/console app:list-users
```

## 🔐 Sécurité

### Rôles utilisateurs

- **ROLE_USER** : Utilisateur standard (accès aux réservations)
- **ROLE_ADMIN** : Administrateur (accès complet au panel admin)

### Protection des routes

- Les routes `/reservation/*` nécessitent une authentification (`ROLE_USER`)
- Les routes `/admin/*` nécessitent le rôle administrateur (`ROLE_ADMIN`)

### Hashage des mots de passe

Les mots de passe sont automatiquement hashés par Symfony Security avec l'algorithme `auto` (bcrypt/argon2i selon la configuration PHP).

## 📊 Modèle de données

### Entité Utilisateur
- `email` (PK) : Identifiant unique
- `password` : Mot de passe hashé
- `nom` : Nom de famille
- `prenom` : Prénom
- `roles` : Tableau des rôles (JSON)

### Entité Spectacle
- `id` (PK) : Identifiant unique
- `titre` : Titre du spectacle
- `prix` : Prix unitaire (DECIMAL)
- `lieu` : Lieu du spectacle
- `image` : URL de l'image (optionnel)
- `placesDisponibles` : Nombre de places disponibles

### Entité Reservation
- `id` (PK) : Identifiant unique
- `utilisateur` : Relation ManyToOne vers Utilisateur
- `spectacle` : Relation ManyToOne vers Spectacle
- `nombrePlaces` : Nombre de places réservées
- `prixUnitaire` : Prix unitaire au moment de la réservation
- `prixTotal` : Prix total de la réservation
- `dateReservation` : Date et heure de la réservation

## 🎨 Personnalisation

### Modifier le thème

Les styles sont gérés via Tailwind CSS. Pour personnaliser :

1. Modifiez les classes Tailwind dans les templates
2. Ou ajoutez des styles personnalisés dans `assets/styles/app.css`

### Ajouter des fonctionnalités

1. **Nouvelle entité** : `php bin/console make:entity`
2. **Nouveau contrôleur** : `php bin/console make:controller`
3. **Nouvelle route** : Ajoutez l'attribut `#[Route]` dans votre contrôleur

## 🐛 Dépannage

### Problème de permissions

```bash
# Donner les permissions d'écriture au dossier var/
chmod -R 777 var/
```

### Erreur de base de données

```bash
# Recréer la base de données
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Problème avec les assets

```bash
# Recompiler les assets
rm -rf public/build
npm run build
```

## 📝 Notes importantes

- Le champ `adresse` dans l'entité `Utilisateur` est présent mais non utilisé dans l'interface
- Les images de spectacles peuvent être des URLs externes ou des chemins locaux
- Les réservations sont simulées (pas de paiement réel)
- Les places disponibles sont automatiquement déduites lors d'une réservation

## 👥 Contributeurs

- **Christian** - Développement initial

## 📄 Licence

Ce projet est sous licence propriétaire.

## 🔗 Liens utiles

- [Documentation Symfony](https://symfony.com/doc/current/index.html)
- [Documentation EasyAdmin](https://symfony.com/bundles/EasyAdminBundle/current/index.html)
- [Documentation Doctrine](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/index.html)
- [Documentation Tailwind CSS](https://tailwindcss.com/docs)

---

**Dernière mise à jour** : Décembre 2024
