[CONSIGNE]

# Travail pratique : Créer une application de sondage (Laravel + Vue.js)

## Introduction

Ce projet vous invite à créer une application complète mêlant backend Laravel et frontend Vue.js,
sous la forme d'un système de sondage multi-plateforme.

L'objectif est de concevoir une interface permettant de créer, configurer, consulter et utiliser des
sondages à travers une API JSON consommée par le frontend.

Dans cette application, un sondage est un objet créé par une personne authentifiée, contenant une
question, plusieurs options de réponse et un ensemble de paramètres définissant son comportement
(brouillon ou lancé, choix simple ou multiple, visibilité des résultats et éventuelle durée de
 disponibilité).

Les modèles Eloquent de base sont fournis. Le travail portera principalement sur :

- le frontend Vue.js
- les endpoints backend JSON nécessaires au fonctionnement du frontend

Le système de sondage attendu repose notamment sur les fonctionnalités UI suivantes :

- afficher à la personne connectée la liste de ses sondages
- permettre à la personne connectée de créer, modifier et supprimer ses sondages
- permettre de définir la question, les options de réponse et les paramètres du sondage
- créer un sondage en mode brouillon, puis permettre de le démarrer soit au moment de sa création,
  soit plus tard
- permettre de configurer si le sondage accepte un choix simple ou plusieurs choix
- permettre de configurer si les résultats sont publics ou non
- permettre de configurer une durée de disponibilité du sondage
- permettre au créateur d'un sondage d'obtenir facilement le lien de partage contenant le token
- afficher une page de vote accessible via un lien contenant un token dans l'URL
- permettre à une personne authentifiée ayant reçu ce lien de voter au sondage
- permettre à une personne non authentifiée ayant reçu ce lien de consulter les résultats si, et
  seulement si, leur visibilité est publique
- afficher sur la page de vote les résultats en direct, via un polling régulier vers l'API
- afficher sur la page de vote un aperçu graphique des résultats ; le type de graphique est libre
- indiquer clairement sur la page de vote qu'il n'est plus possible de voter lorsque la date de fin
  d'un sondage avec durée est dépassée

## Objectifs pédagogiques

À l'issue de ce travail pratique, les étudiants devraient être capables de :

- concevoir et développer une application web frontend complète avec Vue.js, organisée de manière
  cohérente selon l'architecture retenue
- implémenter et consommer une API JSON versionnée
- exploiter des modèles relationnels déjà fournis pour construire des fonctionnalités cohérentes
- créer un frontend réactif pour gérer un tableau de bord, un éditeur et des vues de
  consultation
- interagir avec une API (`GET`, `POST`, `PUT`/`PATCH`, `DELETE`) et afficher
  dynamiquement les contenus

## Consignes générales

Vous développerez une application web en deux parties :

- Backend Laravel : responsable de l'exposition des endpoints JSON utilisés par le frontend
- Frontend Vue.js : responsable de l'affichage et des interactions autour des sondages, utilisable
  sur navigateur et mobile, avec une approche mobile first

L'architecture frontend est libre : il est possible de réaliser soit une seule application Vue.js
couvrant l'ensemble des usages, soit plusieurs applications Vue.js distinctes (par exemple une pour
le dashboard et une pour la consultation, le vote et la visualisation des résultats), à condition
que l'ensemble reste cohérent, maintenable et bien intégré au backend.

Le système d'authentification est déjà en place. Il est externe à l'application Vue.js, hors du
périmètre du travail, et doit être conservé en l'état. Le frontend demandé doit s'intégrer à ce
mécanisme existant, sans le redévelopper ni le modifier.

Un fichier `README_FRONT.md` est fourni à la racine du projet pour documenter l'intégration
frontend existante. Des exemples de fetch vers l'API, d'intégration de plusieurs applications Vue
et d'eager loading sont déjà disponibles dans le code fourni, notamment dans les vues et fichiers
liés aux sondages ainsi que dans certains contrôleurs API. Ces exemples peuvent servir de base de
travail et de référence.

Les modèles sont déjà fournis. Vous devez construire autour de ceux-ci les fonctionnalités utiles au
frontend. Les modèles existants permettent naturellement de représenter plusieurs choix pour un même sondage.
Par conséquent, lorsqu'un sondage est configuré en choix unique, l'unicité du vote doit être
garantie à la fois côté frontend et côté API.

Fonctionnalités attendues :

- afficher la liste des sondages de la personne connectée
- permettre la création, l'édition et la suppression d'un sondage depuis le frontend
- gérer les options d'un sondage
- gérer les paramètres du sondage (brouillon, lancement, choix simple ou multiple, résultats publics,
  dates ou durée)
- permettre au créateur d'obtenir facilement le lien de partage contenant le token
- afficher un sondage accessible via un token
- permettre à une personne authentifiée de voter via ce lien
- empêcher le vote après la date de fin d'un sondage avec durée, avec un affichage clair de cet état
- permettre l'accès aux résultats uniquement lorsqu'ils sont publics (ou si l'on est son propritaire)
- afficher les résultats via polling avec un aperçu graphique visualisant leur évolution
- garantir côté frontend et côté API l'unicité du vote pour les sondages à choix unique

Bonus possible :

- permettre de configurer si le vote peut être modifié après soumission
- permettre, si le sondage l'autorise, de modifier un vote déjà soumis

La structure exacte de l'interface est libre, à condition que l'application reste claire,
fonctionnelle et cohérente.

## Évaluation

Chaque partie du projet sera évaluée selon plusieurs catégories. L'évaluation portera sur :

- la qualité du frontend
- le bon fonctionnement des endpoints JSON nécessaires à ce frontend
- la capacité à expliquer, défendre et adapter son code à l'oral

Conditions particulières :

- toute triche avérée entraîne la note de `1` et aucune possibilité de remédiation ne sera proposée
- l'oral a un poids important dans l'évaluation afin de contrebalancer l'usage des IA et de vérifier
  la maîtrise réelle du travail rendu

Note maximale : `(nombre de points obtenus / nombre de points maximum) x 5 + 1`

## Critères frontend et endpoints JSON

Les informations ci-dessous sont à titre indicatif et peuvent être adaptées.

### Critères rendu

| # | Critère |
| --- | --- |
| 1 | Affichage d'un dashboard des sondages de la personne connectée
| 2 | Création, édition et suppression d'un sondage depuis le frontend
| 3 | Gestion des options du sondage (ajout, modification, suppression)
| 4 | Gestion des paramètres du sondage (brouillon, choix multiples, résultats publics, durée)
| 5 | Récupération simple du lien de partage contenant le token et affichage d'un sondage accessible via ce lien
| 6 | Soumission d'un vote valide depuis le frontend, avec unicité correctement garantie pour les sondages à choix unique
| 7 | Affichage conditionnel correct selon l'état du sondage, la date de fin et les droits d'accès, y compris l'accès aux résultats si publics
| 8 | Consommation correcte des endpoints JSON par le frontend
| 9 | Gestion correcte des erreurs utilisateur côté frontend
| 10 | Interface lisible, claire, responsive et agréable à utiliser
| 11 | Affichage en temps réel, via polling, des résultats, avec aperçu graphique
| 12 | Le projet est fonctionnel de bout en bout
| 13 | Code lisible, structuré, `README` clair et utilisation correcte du contrôle de version
| 14 | Bon usage des composants Vue, des composables et d'une architecture cohérente du code
| 15 | Nommage, lisibilité et organisation générale du frontend (et routes API backend) soignés

Bonus possible : prise en charge du changement de vote lorsqu'un sondage l'autorise


## Critères présentation

| # | Critère |
| --- | --- |
| 1 | Les informations sont claires et bien présentées
| 2 | Les réponses aux questions sont pertinentes
| 3 | La capacité à modifier le code en direct selon une demande est satisfaisante
| 4 | La compréhension théorique de Vue.js, des échanges frontend/backend et de l'architecture fullstack est bonne
| 5 | La personne démontre qu'elle maîtrise réellement le code présenté, y compris si des outils d'IA ont été utilisés

Vous devrez présenter votre architecture lors de l’oral en 10 min. Voici quelques points que votre présentation pourrait inclure :

- Montrer les fonctionnalités implémentées via une courte démo de l’application (max. 5 min)
- Expliquer pourquoi vous avez fait tel composant
- Expliquer pourquoi vous êtes passé par un store, un composable ou autre
- Expliquer la gestion des échanges front-back
- Expliquer la stack technique et les bibliothèques utilisées

S’ensuivra une phase de questions où vous devriez être capable de :

- Expliquer un usage de la réactivité
- Montrer la maîtrise de votre code (y compris celui de l'IA si utilisée)
- Démontrer que vous comprenez l’architecture implémentée
- Modifier en "live" votre code pour une adaptation mineure, comme par exemple le rajout d’une fonctionnalité simple
- Répondre à des questions théoriques sur la réactivité (ex. : « Est-ce qu’une "computed" aurait aussi fait l’affaire à cet endroit ? », « Qu’apporte votre utilisation d’un store par rapport à un passage via des propriétés ? », etc.)


## Contraintes techniques

- Backend Laravel >= 12.x
- Frontend Vue.js >= 3.4
- Base de données relationnelle (`SQLite`, `MySQL` ou `PostgreSQL`)
- Projet disponible sur GitHub
- Une documentation minimale (`README.md`) doit permettre de tester facilement l'application
- Les modèles et migrations sont fournis (mais sont modifiable). Les endpoints JSON nécessaires au frontend doivent
  être implémentés
- L'usage de l'IA est autorisé, mais le code rendu doit être compris, maîtrisé et défendable à l'oral
- Les critères liés à l'architecture, au découpage du code, au nommage et à la lisibilité auront une
  importance particulière
- L'usage d'outils d'IA ne dispense pas d'un regard critique : un code trop verbeux, mal structuré ou
  peu cohérent sera pénalisé

## Conseils

- Ne cherchez pas à faire complexe : commencez simple, itérez ensuite.
- Travaillez de manière incrémentale et validez chaque étape.
- Testez tôt et souvent.
- Une fonctionnalité simple mais fiable vaut mieux qu'une fonctionnalité ambitieuse inachevée.
- Structurez clairement les données échangées entre votre frontend et votre API JSON.

## Livrables et rendu

Vous devez fournir :

- l'URL du dépôt GitHub
- un fichier `README.md` clair pour expliquer l'installation et les choix techniques (pas besoin de rentrer dans les détails)
- Il est possible de mettre à jour le dépôt entre le jour du rendu et l'examen
- Seul le code présent avant l'échéance sera évalué pour le rendu
- Le code ajouté ou modifié après l'échéance ne sera pas évalué pour la note de rendu, mais pourra
  éventuellement aider lors de la présentation orale

Rendu final : au plus tard le dimanche 17 mai 2026 à 23:59:59 UTC (date du commit).

La présentation orale aura lieu lors de la période des examens et sera probablement d'une durée de 20 minutes par étudiant.

-----------------------------------------------------------------------------------------------------
[ARCHITECTURE]

## Architecture retenue

L'interface des sondages sera une SPA Vue.js servie par Laravel.
Elle sera chargee par `/polls/dashboard` pour la gestion des sondages et par `/polls/vote/{token}`
pour acceder a un sondage via son lien de partage.
Cette SPA affichera un seul ecran a la fois : liste, creation, edition, vote ou resultats.

L'application est organisee autour d'une separation simple :

- Laravel sert les pages, garde l'authentification existante et expose une API JSON versionnée.
- Vue.js gère l'interface des sondages : dashboard, édition, vote et résultats.
- La base de données relationnelle stocke les utilisateurs, les sondages, les options et les votes.
- Tailwind pour le style

## Fichiers a modifier / creer

```txt
app/
├── Http/Controllers/Api/v1/
│   └── ApiPollController.php              -> MODIFIER : CRUD, lien token, vote, resultats
│
├── Http/Requests/
│   ├── StorePollRequest.php               -> CREER : validation creation sondage
│   ├── UpdatePollRequest.php              -> CREER : validation edition sondage
│   └── StorePollVoteRequest.php           -> CREER : validation vote
│
└── Models/
    ├── Poll.php                           -> MODIFIER legerement : fillable, casts, helpers
    ├── PollOption.php                     -> MODIFIER legerement si necessaire
    └── PollVote.php                       -> MODIFIER legerement si necessaire
```

```txt
resources/js/
├── poll-dashboard.js                      -> GARDER : entrypoint existant
├── AppPollDashboard.vue                   -> MODIFIER : application principale des sondages
├── components/
│   ├── PollTable.vue                      -> MODIFIER : liste, statuts, actions
│   ├── PollEditor.vue                     -> CREER : creation / edition
│   ├── PollOptionsEditor.vue              -> CREER : ajout / modification / suppression options
│   ├── PollSettings.vue                   -> CREER : brouillon, lancement, choix multiple, public, duree
│   ├── PollShareLink.vue                  -> CREER : affichage / copie du lien token
│   ├── PollVote.vue                       -> CREER : vote simple ou multiple
│   ├── PollResults.vue                    -> CREER : resultats + graphique simple
│   └── UserFeedback.vue                   -> CREER SI UTILE : erreurs et messages utilisateur
│
└── composables/
    ├── useFetchApi.js                     -> GARDER : appels JSON existants
    ├── usePolls.js                        -> CREER : liste, creation, edition, suppression
    ├── usePollVoting.js                   -> CREER : vote et etat du vote
    ├── usePollResults.js                  -> CREER : chargement resultats
    └── usePolling.js                      -> GARDER / ADAPTER : refresh resultats
```

```txt
resources/views/polls/
├── dashboard.blade.php                    -> GARDER : charge l'app Vue en mode dashboard
└── vote.blade.php                         -> CREER : charge la meme app Vue avec un token
```

```txt
routes/
├── web.php                                -> AJUSTER : /polls/dashboard et /polls/vote/{token}
└── api.php                                -> MODIFIER : endpoints JSON consommes par Vue
```

```txt
README.md                                  -> MODIFIER : installation, lancement, choix techniques
```

## Endpoints API a exposer

```txt
GET    /api/v1/polls                       -> liste des sondages du user connecte
POST   /api/v1/polls                       -> creation d'un sondage
GET    /api/v1/polls/{poll}                -> detail proprietaire
PATCH  /api/v1/polls/{poll}                -> edition d'un sondage
DELETE /api/v1/polls/{poll}                -> suppression d'un sondage

GET    /api/v1/polls/token/{token}         -> affichage via lien partage
POST   /api/v1/polls/token/{token}/vote    -> vote
GET    /api/v1/polls/token/{token}/results -> resultats avec polling
```

## Ecrans internes de la SPA

```txt
AppPollDashboard.vue
├── view = "list"       -> PollTable
├── view = "create"     -> PollEditor vide
├── view = "edit"       -> PollEditor rempli
├── view = "vote"       -> PollVote
└── view = "results"    -> PollResults
```

## Points de controle

- Travailler principalement dans l'interface Vue servie par `/polls/dashboard`.
- Utiliser `/polls/vote/{token}` pour le lien public de vote/resultats.
- Ne pas refaire l'authentification ni la structure generale Laravel.
- Ajouter seulement les endpoints JSON necessaires au frontend.
- Garder le backend comme couche de validation, de droits et de persistance.
- Garantir cote API les regles importantes : proprietaire, brouillon, date de fin, choix unique, resultats publics.
- Gerer les erreurs utilisateur cote frontend : validation, acces refuse, sondage termine, vote impossible.
- Afficher les resultats avec polling regulier et graphique simple.
- Garder une interface Vue propre, lisible et responsive.

-----------------------------------------------------------------------------------------------------
[PLAN]

## Roadmap

---

### Etape 0 — Exploration et preparation

Avant d'ecrire une seule ligne de code, comprendre ce qui existe deja.

**Lire et comprendre :**
- `routes/web.php` — quelles routes existent, comment les vues sont servies
- `routes/api.php` — ce qui est deja expose en JSON
- `app/Models/Poll.php`, `PollOption.php`, `PollVote.php` — champs, relations, fillable
- `database/migrations/` — structure exacte des tables (colonnes, types, nullable)
- `resources/js/poll-dashboard.js` — comment l'app Vue est montee
- `resources/js/AppPollDashboard.vue` — etat actuel du composant racine
- `resources/js/composables/useFetchApi.js` — comment les appels API sont faits
- `resources/views/polls/dashboard.blade.php` — comment la vue Blade charge Vue
- `README_FRONT.md` — exemples fournis d'integration Vue dans Laravel

**Questions a repondre avant de coder :**
- Quels champs existent sur `polls` ? (`is_draft`, `secret_token`, `allow_multiple_choices`, `results_public`, `duration`, `started_at`, `ends_at` ou autre ?)
- `PollVote` contient-il `user_id` + `poll_option_id` ?
- Le token est-il deja dans le modele ou a creer ?
- `useFetchApi` gere-t-il le CSRF / les headers JSON automatiquement ?
- Comment le `user()` authentifie est-il accessible dans les controleurs API ?

---

### Etape 1 — Backend : preparer les modeles

**Fichier : `app/Models/Poll.php`**
- Verifier et completer le tableau `$fillable` avec tous les champs utiles
- Ajouter les `$casts` necessaires : booléens (`is_draft`, `allow_multiple_choices`, `results_public`), dates (`started_at`, `ends_at`)
- Verifier les relations `options()` et `votes()` (HasMany)
- Ajouter si besoin un helper `isExpired()` ou `isActive()` pour eviter de repeter la logique

**Fichier : `app/Models/PollOption.php`**
- Verifier `$fillable` (au minimum `label`, `poll_id`)
- Verifier la relation inverse vers `Poll` (BelongsTo)

**Fichier : `app/Models/PollVote.php`**
- Verifier `$fillable` (`user_id`, `poll_option_id`, `poll_id` si present)
- Verifier les relations : `option()` (BelongsTo PollOption), `user()` (BelongsTo User)

---

### Etape 2 — Backend : routes API

**Fichier : `routes/api.php`**

Ajouter les routes dans un groupe `prefix('v1')` avec middleware `auth:sanctum` (ou le middleware auth existant) :

```
GET    /api/v1/polls
POST   /api/v1/polls
GET    /api/v1/polls/{poll}
PATCH  /api/v1/polls/{poll}
DELETE /api/v1/polls/{poll}
```

Et les routes publiques (sans auth obligatoire) :
```
GET    /api/v1/polls/token/{token}
POST   /api/v1/polls/token/{token}/vote       <- auth requise
GET    /api/v1/polls/token/{token}/results
```

**Attention :** declarer les routes `/token/{token}` AVANT `/{poll}` dans le fichier pour eviter le conflit de routing Laravel.

**Fichier : `routes/web.php`**
- Ajouter la route `GET /polls/vote/{token}` qui retourne la vue `polls.vote`
- Verifier que `GET /polls/dashboard` existe et retourne la bonne vue

---

### Etape 3 — Backend : FormRequests

**Fichier a creer : `app/Http/Requests/StorePollRequest.php`**

Generer avec `php artisan make:request StorePollRequest`.
Regler `authorize()` a `true`.
Definir les regles pour : `question` (required, string), `options` (required, array, min:2), `options.*.label` (required, string), `allow_multiple_choices` (boolean), `results_public` (boolean), `duration` (nullable, integer, min:60).

**Fichier a creer : `app/Http/Requests/UpdatePollRequest.php`**

Memes regles que Store, mais tout en `sometimes` (les champs sont optionnels en edition).

**Fichier a creer : `app/Http/Requests/StorePollVoteRequest.php`**

Regle pour : `option_ids` (required, array), `option_ids.*` (exists dans `poll_options`).

---

### Etape 4 — Backend : controleur API

**Fichier : `app/Http/Controllers/Api/v1/ApiPollController.php`**

Implementer les methodes dans cet ordre :

1. **`index`** — retourner les sondages du user connecte avec leurs options (`with('options')`), triees par date desc

2. **`store`** — creer un sondage, generer un `secret_token` unique (`Str::random(32)`), associer le user, sauvegarder les options en masse, retourner 201

3. **`show`** — verifier que le user est bien proprietaire (`$poll->user_id !== auth()->id()` → 403), retourner le sondage avec ses options et ses votes si proprietaire

4. **`update`** — verifier proprietaire, mettre a jour les champs du sondage, synchroniser les options (supprimer les anciennes, inserer les nouvelles), retourner le sondage mis a jour

5. **`destroy`** — verifier proprietaire, supprimer le sondage (et options + votes en cascade si configure), retourner 204

6. **`showByToken`** — trouver le sondage via `secret_token`, verifier qu'il n'est pas brouillon, retourner les donnees publiques (question, options, paramètres mais pas les votes si resultats non publics)

7. **`vote`** — trouver via token, verifier : pas brouillon, pas expire, user authentifie. Pour choix unique : verifier qu'aucun vote de ce user n'existe deja sur ce sondage. Enregistrer le ou les votes. Retourner confirmation.

8. **`results`** — trouver via token, verifier visibilite : si `results_public = false`, seul le proprietaire (authentifie) peut voir. Retourner les options avec le compte de votes pour chacune.

**Regles metier a verifier dans chaque methode cote API (jamais laisser ca qu'au frontend) :**
- Proprietaire : seul le createur peut modifier/supprimer
- Brouillon : pas de vote sur un sondage en `is_draft = true`
- Expire : verifier `ends_at` si defini, refuser si depasse
- Choix unique : un seul vote par user par sondage
- Resultats : respecter `results_public`

---

### Etape 5 — Backend : vue Blade pour le vote

**Fichier a creer : `resources/views/polls/vote.blade.php`**

Copier la structure de `dashboard.blade.php` et l'adapter :
- Charger le meme entrypoint JS (`poll-dashboard.js`)
- Passer le token en variable JS accessible par Vue (via `@json` ou `data-*` attribute sur le div de montage)

---

### Etape 6 — Frontend : composables

Travailler dans `resources/js/composables/`.

**`useFetchApi.js`** — ne pas modifier, juste comprendre comment l'utiliser (url, method, body).

**`usePolling.js`** — adapter ou creer :
- Fonction qui prend une callback et un intervalle (ex. 5000ms)
- Lance `setInterval` au montage
- Stoppe proprement avec `clearInterval` dans `onUnmounted`

**`usePolls.js`** — creer :
- `polls` : ref tableau
- `loading`, `error` : refs
- `fetchPolls()` : GET `/api/v1/polls`
- `createPoll(data)` : POST `/api/v1/polls`
- `updatePoll(id, data)` : PATCH `/api/v1/polls/{id}`
- `deletePoll(id)` : DELETE `/api/v1/polls/{id}`

**`usePollVoting.js`** — creer :
- `poll` : ref (le sondage charge via token)
- `selectedOptions` : ref tableau (ids selectionnes)
- `hasVoted`, `loading`, `error` : refs
- `fetchPollByToken(token)` : GET `/api/v1/polls/token/{token}`
- `submitVote(token)` : POST `/api/v1/polls/token/{token}/vote`

**`usePollResults.js`** — creer :
- `results` : ref (options avec comptes)
- `loading`, `error` : refs
- `fetchResults(token)` : GET `/api/v1/polls/token/{token}/results`

---

### Etape 7 — Frontend : composants

Travailler dans `resources/js/components/`.

Ordre conseille : du plus simple au plus complexe.

**`PollShareLink.vue`**
- Affiche l'URL de partage construite a partir du `secret_token`
- Bouton "Copier" (utiliser `navigator.clipboard.writeText`)
- Props : `token` (string)

**`PollTable.vue`** (modifier l'existant)
- Affiche la liste des sondages en tableau ou cards
- Pour chaque sondage : question, statut (brouillon / actif / expire), boutons Editer / Supprimer / Voir resultats / Lien partage
- Emet : `@create`, `@edit(poll)`, `@delete(poll)`, `@results(poll)`

**`PollOptionsEditor.vue`**
- Affiche la liste des options actuelles (inputs editables)
- Bouton "Ajouter une option" (ajoute un champ vide)
- Bouton supprimer sur chaque option
- Props : `modelValue` (array d'options), emet `update:modelValue` (v-model compatible)

**`PollSettings.vue`**
- Checkboxes / toggles pour : `allow_multiple_choices`, `results_public`
- Input pour `duration` (en secondes ou minutes, a decider)
- Bouton "Lancer le sondage" (passe `is_draft` a false)
- Props : `modelValue` (objet settings), emet `update:modelValue`

**`PollEditor.vue`**
- Formulaire complet de creation / edition
- Inclut `PollOptionsEditor` et `PollSettings`
- Gere l'etat local du formulaire
- A la soumission : appelle `createPoll` ou `updatePoll` selon le contexte
- Props : `poll` (null si creation, objet si edition)
- Emet : `@saved(poll)`, `@cancel`

**`PollVote.vue`**
- Affiche la question et les options sous forme de radios (choix unique) ou checkboxes (choix multiple)
- Verifie si le sondage est expire → affiche message "Sondage termine"
- Verifie si l'user a deja vote → affiche message ou desactive le formulaire
- Bouton "Voter" → appelle `submitVote`
- Apres vote reussi : emet `@voted`
- Props : `token` (string)

**`PollResults.vue`**
- Affiche les resultats avec le nombre de votes par option
- Graphique simple : barres en pur CSS (largeur = pourcentage) ou avec une lib legere si besoin
- Rafraichit via `usePolling` + `fetchResults` toutes les 5 secondes
- Stoppe le polling dans `onUnmounted`
- Props : `token` (string), `isOwner` (boolean)
- Emet : `@back`

---

### Etape 8 — Frontend : composant racine et entrypoint

**Fichier : `resources/js/AppPollDashboard.vue`**

- Definir `view` comme `ref('list')` ou `ref('vote')` selon le contexte (dashboard vs lien partage)
- Detecter si un token est present (via un `data-*` attribute sur le div de montage)
- Charger les sondages au montage si mode dashboard
- Gerer les transitions entre vues : `list` → `create` → `edit` → `results`
- Passer les bons props a chaque composant enfant
- Ecouter les events pour naviguer entre les vues

**Fichier : `resources/js/poll-dashboard.js`**
- Verifier que le montage Vue passe bien le token si present dans le DOM

---

### Etape 9 — Verifications et cas limites

Tester manuellement chaque cas :

**Dashboard**
- [ ] La liste s'affiche correctement
- [ ] Creer un sondage avec au moins 2 options → apparait en brouillon
- [ ] Lancer un sondage → statut change
- [ ] Editer un sondage (question, options, parametres)
- [ ] Supprimer un sondage → disparait de la liste
- [ ] Copier le lien de partage → URL correcte

**Vote**
- [ ] Ouvrir le lien de partage → sondage s'affiche
- [ ] Voter (choix unique) → confirmation
- [ ] Voter une seconde fois → refuse (frontend + API)
- [ ] Ouvrir un sondage en brouillon via token → acces refuse
- [ ] Ouvrir un sondage expire → message "termine", bouton vote desactive

**Resultats**
- [ ] Resultats visibles en temps reel (polling)
- [ ] Graphique s'affiche avec les bons pourcentages
- [ ] Sondage avec `results_public = false` : resultats visibles seulement par proprietaire
- [ ] Polling stoppe quand on quitte la vue (verifier dans l'onglet Network que les requetes s'arretent)

**Erreurs utilisateur**
- [ ] Formulaire incomplet → messages d'erreur clairs
- [ ] Erreur API (403, 422, 500) → message affiche, pas de crash silencieux

---

### Etape 10 — Finition

**`README.md`**
- Etapes d'installation (`composer install`, `npm install`, migration, `.env`)
- Comment lancer le projet (`php artisan serve`, `npm run dev`)
- Choix techniques : pourquoi une SPA, pourquoi ces composables, quelle lib graphique si utilisee

**Controle de version**
- Commits reguliers et messages clairs (pas un seul commit a la fin)
- `.env` non commite, `.env.example` present et a jour

**Relecture finale**
- Supprimer les `console.log` laisses en debug
- Verifier que les noms de variables, composants et routes sont coherents
- S'assurer que chaque composant peut etre explique en 2 phrases a l'oral
