# Touche pas au klaxon

Application intranet de covoiturage entre les agences d'une entreprise multi-sites.
Les employés consultent les trajets prévus entre agences, proposent les leurs et contactent
les conducteurs. Un administrateur gère les agences et supervise les trajets.

Projet réalisé dans le cadre de la formation Développeur Web et Web Mobile
(Centre Européen de Formation) : **application MVC en PHP**.

---

## Sommaire

- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Comptes de test](#comptes-de-test)
- [Utilisation](#utilisation)
- [Architecture](#architecture)
- [Base de données](#base-de-données)
- [Qualité du code](#qualité-du-code)
- [Sécurité](#sécurité)
- [Personnaliser l'application (autres sites de l'intranet)](#personnaliser-lapplication-autres-sites-de-lintranet)
- [Limites et évolutions](#limites-et-évolutions)

---

## Fonctionnalités

**Visiteur (non connecté)**
- Liste des trajets à venir qui ont encore des places, triés par date de départ.

**Employé connecté**
- Détail d'un trajet dans une fenêtre modale : auteur, téléphone, email, nombre total de places.
- Création d'un trajet (coordonnées de l'auteur pré-remplies et non modifiables).
- Modification et suppression de ses propres trajets.

**Administrateur**
- Tableau de bord.
- Liste des utilisateurs (lecture seule : les employés proviennent du SI RH).
- Liste, création, modification et suppression des agences
  (une agence utilisée par un trajet ne peut pas être supprimée).
- Liste de tous les trajets (passés et complets compris) et suppression.

Après chaque écriture en base, l'utilisateur est redirigé vers la liste correspondante
avec un message de confirmation (message flash).

---

## Stack technique

| Couche | Technologie |
|---|---|
| Langage | PHP 8.2+ (orienté objet, `strict_types`) |
| Architecture | MVC maison, routeur [izniburak/router](https://packagist.org/packages/izniburak/router) |
| Base de données | MariaDB 10.4+ ou MySQL 8+, accès via PDO (requêtes préparées) |
| Interface | Bootstrap 5.3 personnalisé avec Sass, Bootstrap Icons |
| Configuration | vlucas/phpdotenv (fichier `.env`) |
| Qualité | PHPUnit 11, PHPStan (niveau 8), PHP_CodeSniffer (PSR-12) |

---

## Prérequis

- **PHP 8.2 ou plus**, avec les extensions `pdo_mysql` et `mbstring`
- **Composer** 2
- **MariaDB** ou **MySQL** (par exemple via XAMPP, Laragon ou WAMP)
- *Facultatif :* **Node.js** et npm, uniquement pour modifier les styles (Sass)

---

## Installation

### 1. Récupérer le projet

```bash
git clone https://github.com/morganancelin-gif/touche-pas-au-klaxon.git
cd touche-pas-au-klaxon
composer install
```

### 2. Créer la base de données

Démarrer MySQL/MariaDB, puis exécuter les trois scripts **dans cet ordre**, avec un compte
administrateur du SGBD (ici `root`) :

```bash
mysql -u root -h 127.0.0.1 < database/schema.sql   # crée la base et les tables
mysql -u root -h 127.0.0.1 < database/user.sql     # crée le compte de l'application
mysql -u root -h 127.0.0.1 < database/seed.sql     # insère le jeu d'essai
```

> Si votre compte `root` a un mot de passe, ajoutez `-p`.
> Avec phpMyAdmin : onglet **Importer**, puis importez les trois fichiers dans le même ordre.
> Sous XAMPP, la commande `mysql` se trouve dans le dossier `mysql/bin` (Windows)
> ou `/Applications/XAMPP/xamppfiles/bin` (macOS).

### 3. Configurer l'application

```bash
cp .env.example .env
```

Les valeurs par défaut correspondent au compte créé par `database/user.sql`.
Adaptez `DB_HOST`, `DB_PORT` et `DB_PASSWORD` si besoin.

> Utilisez `127.0.0.1` plutôt que `localhost` pour `DB_HOST` : sur macOS notamment,
> `localhost` force une connexion par socket qui peut échouer.

### 4. Lancer l'application

```bash
composer start
```

Ouvrir **http://localhost:4000**.

> Les styles compilés (`public/css/app.css`) et le JavaScript de Bootstrap (`public/js/`)
> sont versionnés : **Node.js n'est pas nécessaire** pour lancer l'application.

---

## Comptes de test

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | `alexandre.martin@email.fr` | `Admin#Klaxon2026` |
| Utilisateur | `sophie.dubois@email.fr` | `User#Klaxon2026` |

Les 18 autres employés du jeu d'essai utilisent aussi le mot de passe `User#Klaxon2026`.
Les mots de passe sont stockés hachés (bcrypt) en base.

Le jeu d'essai utilise des **dates relatives** à la date d'import : les trajets restent
« à venir » quel que soit le jour où il est importé. Il contient aussi un trajet complet
et un trajet passé, qui ne doivent pas apparaître sur la page d'accueil.

---

## Utilisation

1. **Sans être connecté**, la page d'accueil liste les trajets disponibles.
2. **Connectez-vous** avec le compte de Sophie : le bouton « œil » affiche le détail d'un trajet ;
   le crayon et la corbeille apparaissent sur ses propres trajets (Paris → Lyon, Paris → Lille).
3. **Créer un trajet** : bouton dans l'en-tête. Les incohérences sont refusées
   (agences identiques, arrivée avant le départ, plus de places disponibles que de places au total…).
4. **Connectez-vous en administrateur** : le tableau de bord donne accès aux utilisateurs,
   aux agences et aux trajets.

---

## Architecture

```
touche-pas-au-klaxon/
├── config/routes.php        Déclaration des routes (et des middlewares)
├── database/                schema.sql, user.sql, seed.sql
├── docs/                    MCD (source Mocodo, SVG, PNG) et MLD textuel
├── public/                  Seul dossier exposé : index.php (front controller), css/, js/
├── scss/                    _variables.scss (palette) et app.scss
├── src/
│   ├── Core/                Noyau réutilisable : Controller, Database, Session, Auth,
│   │                        Csrf, Flash, helpers.php
│   ├── Controllers/         Home, Auth, Trajet, Admin, Agence
│   ├── Middlewares/         Auth, Admin, Csrf
│   ├── Models/              Accès aux données (PDO) : Employe, Agence, Trajet
│   └── Validators/          Validation des formulaires : Trajet, Agence
├── tests/                   Tests PHPUnit (modèles et validateurs)
└── views/                   Layout, partials (header, footer), pages, icônes SVG
```

**Cycle d'une requête :** `public/index.php` charge la configuration et la session,
le routeur choisit le contrôleur (après les middlewares éventuels), le contrôleur
interroge le modèle puis rend une vue dans le layout commun.

Le dossier `src/Core/` ne contient rien de propre au covoiturage : il peut être repris
tel quel pour les autres sites de l'intranet.

---

## Base de données

- **MCD :** [`docs/mcd.png`](docs/mcd.png) (source : `docs/mcd.mcd`, à ouvrir avec [Mocodo](https://www.mocodo.net))
- **MLD :** [`docs/mld.txt`](docs/mld.txt)
- **Script de création :** [`database/schema.sql`](database/schema.sql)
- **Jeu d'essai :** [`database/seed.sql`](database/seed.sql)

Les règles de gestion sont garanties deux fois : par la validation PHP (messages clairs pour
l'utilisateur) et par des contraintes SQL (`CHECK`, `UNIQUE`, clés étrangères `ON DELETE RESTRICT`).

---

## Qualité du code

```bash
composer check     # lance les trois contrôles ci-dessous
composer cs        # norme PSR-12 (PHP_CodeSniffer) ; composer cs-fix corrige automatiquement
composer stan      # analyse statique PHPStan, niveau 8
composer test      # tests PHPUnit
```

Les tests des modèles écrivent dans la base configurée dans `.env`. Chaque test s'exécute dans
une **transaction annulée à la fin** : la base retrouve toujours son état initial.
MySQL/MariaDB doit être démarré.

Le code est documenté avec des **DocBlocks** (classes, méthodes, paramètres, retours).

---

## Sécurité

| Risque | Protection |
|---|---|
| Injection SQL | Requêtes préparées PDO, émulation désactivée |
| XSS | Échappement systématique des affichages (`e()`) |
| CSRF | Jeton secret dans chaque formulaire POST, vérifié par un middleware |
| Vol / fixation de session | Cookie `httponly` et `samesite`, mode strict, régénération de l'identifiant à la connexion |
| Accès non autorisé | Middlewares d'authentification et d'administration ; contrôle de l'auteur côté serveur (IDOR) |
| Mots de passe | Hachage bcrypt (`password_hash` / `password_verify`) |
| Secrets | Identifiants de la base dans `.env`, non versionné |
| SGBD | Compte applicatif limité au CRUD sur une seule base (moindre privilège) |

---

## Personnaliser l'application (autres sites de l'intranet)

La palette est définie dans **un seul fichier**, via les variables de Bootstrap :

```scss
// scss/_variables.scss
$primary:   #0074c7;
$secondary: #00497c;
$dark:      #384050;
...
```

Après modification, recompiler les styles (Node.js requis) :

```bash
npm install
npm run build        # ou npm run sass:watch pendant le développement
```

---

## Limites et évolutions

- **Pas de réservation** : le nombre de places disponibles est mis à jour par l'auteur du trajet.
  Une évolution naturelle serait une table `reservation` (association entre employés et trajets).
- **Mise en production** : passer en HTTPS (le cookie de session devient alors `secure`
  automatiquement), mettre `APP_DEBUG=false` et changer le mot de passe du compte `klaxon_app`.
- **Renforcement possible** : limitation des tentatives de connexion, en-têtes de sécurité HTTP
  (Content-Security-Policy…), expiration de session après inactivité.
