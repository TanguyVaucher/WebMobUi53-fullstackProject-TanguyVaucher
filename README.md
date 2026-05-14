# Module de sondages - Tanguy Vaucher

Ce dépôt est basé sur le mini-projet Laravel de Monsieur Ludovic Delafontaine, réalisé dans le cadre du cours DevProdMéd à la HEIG-VD.

Le projet de base est une application Laravel de type réseau social. Dans ce rendu, un module de sondages a été ajouté avec une interface frontend en Vue.js. Ce module permet de créer, configurer, partager, voter et consulter les résultats de sondages depuis une interface intégrée à Laravel.

Une attention particulière a été donnée à l'architecture frontend, aux choix techniques, à la lisibilité du code et à l'expérience utilisateur (UX/UI). L'affichage est conçu pour un affichage desktop, le responsive n'est pas pris en charge.

## Technologies utilisées

- Backend : Laravel 12
- Utilisation du système d'authentification existante et de l'API JSON
- Vue.js 3 pour l'interface du module de sondages
- Vite pour la compilation des assets frontend
- Tailwind CSS pour le style et l'interface responsive
- SQLite par défaut pour la base de données locale
- Laravel Sanctum pour l'authentification des appels API

## Architecture du module de sondages

Le module de sondages est chargé depuis Laravel, puis géré côté frontend par une application Vue.

Les pages principales sont :

- `/polls/dashboard` : tableau de bord des sondages de l'utilisateur connecté
- `/polls/vote/{token}` : page de vote accessible via un lien de partage

Le frontend consomme une API JSON versionnée sous `/api/v1/polls`. Les appels API permettent de gérer les sondages, les options, le vote et les résultats.

Le code Vue est découpé en composants et composables :

- composants pour le tableau de bord, l'édition, les options, les paramètres, le vote et les résultats
- composables pour les appels API, le CRUD des sondages, le vote, les résultats et le polling

## Utilisation des IA

Des outils d'IA, principalement Claude et Codex, ont été utilisés comme aide au développement.

Ils ont servi pour :

- la planification de l'architecture
- l'aide à l'écriture de certaines parties du code
- La correction des commentaires
- l'aide à la conception de l'interface avec Tailwind CSS
- La correction de problèmes techniques

## Installation

Installer les dépendances :

```bash
composer install
npm install
```

Créer le fichier d'environnement :

```bash
cp .env.example .env
```

Générer la clé Laravel :

```bash
php artisan key:generate
```

Préparer la base de données :

```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed
```

## Lancement du projet

Lancer le projet :

```bash
composer run dev
```

L'application est ensuite accessible à l'adresse :

```txt
http://127.0.0.1:8000
http://127.0.0.1:8000/polls/dashboard
```

## Accès au compte avec des polls

Pour accéder à un compte de test avec des sondages déjà créés, utiliser :

- Email : `test@full.poll`
- Mot de passe : `Pwd123456789`
