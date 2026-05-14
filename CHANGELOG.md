# CHANGELOG

Historique complet de toutes les modifications apportées au projet depuis sa création,
classées chronologiquement (du plus ancien au plus récent), séparées par couche technique,
et mises en regard des critères de PROJET2.md.

---

## 23740de — Initial commit _(socle de départ)_

> Projet Laravel fourni par le professeur. Aucun code étudiant.

### Backend — Fichiers créés
| Fichier | Rôle |
|---|---|
| `app/Http/Controllers/Api/v1/ApiPostController.php` | Contrôleur API exemple (posts) |
| `app/Http/Controllers/Auth*`, `PostController.php`, etc. | Contrôleurs métier existants |
| `app/Models/User.php`, `Post.php`, `Like.php` | Modèles fournis |
| `database/migrations/` (sessions, cache, jobs, users, posts, likes, tokens) | Migrations de base |
| `routes/api.php`, `routes/web.php` | Routes initiales |
| `config/` (app, auth, cache, database, sanctum…) | Configuration Laravel |
| `resources/views/` (auth, posts, profile…) | Vues Blade existantes |

### Frontend — Fichiers créés
| Fichier | Rôle |
|---|---|
| `resources/js/app.js` | Entrypoint JS initial |
| `resources/js/bootstrap.js` | Bootstrap Axios |
| `resources/css/app.css` | CSS de base |
| `vite.config.js` | Config Vite initiale |

---

## ab74d6b — Vue setup

> Installation et configuration de Vue.js dans le projet Laravel.

### Backend — Fichiers modifiés
| Fichier | Action |
|---|---|
| `routes/web.php` | Ajout routes pour les vues Blade poll builder et poll results |
| `resources/views/components/default-layout.blade.php` | Mise à jour layout par défaut |

### Frontend — Fichiers créés
| Fichier | Rôle |
|---|---|
| `resources/js/App.vue` | Composant Vue racine initial |
| `resources/js/poll-builder.js` | Entrypoint JS pour le builder de sondage |
| `resources/js/poll-results.js` | Entrypoint JS pour les résultats |
| `resources/js/polls/PollBuilderApp.vue` | SPA builder initiale |
| `resources/js/polls/PollResultsApp.vue` | SPA résultats initiale |
| `resources/views/polls/builder.blade.php` | Vue Blade pour le builder |
| `resources/views/polls/results.blade.php` | Vue Blade pour les résultats |

### Frontend — Fichiers modifiés
| Fichier | Action |
|---|---|
| `package.json` / `package-lock.json` | Ajout dépendances Vue.js |
| `vite.config.js` | Configuration multi-entrypoints Vue |

---

## 733bb6a — feat: integrate Vue.js SPAs with Sanctum session-based auth

> Intégration complète de l'authentification Sanctum côté SPA Vue (cookies de session).
> Mise en place de l'architecture de base composables + utils.

### Backend — Fichiers créés / modifiés
| Fichier | Action |
|---|---|
| `app/Http/Controllers/Api/v1/ApiFooController.php` | Contrôleur de test API (exemple fetch) |
| `bootstrap/app.php` | Ajout middleware CORS / Sanctum |
| `routes/api.php` | Ajout routes API initiales |
| `routes/web.php` | Mise à jour routes web |
| `.env.example` | Ajout variables d'environnement Sanctum |

### Frontend — Fichiers créés
| Fichier | Rôle |
|---|---|
| `resources/js/bootstrap.js` | Configuration Axios avec `withCredentials`, base URL `/api/v1` |
| `resources/js/AppPollBuilder.vue` | SPA builder (remplace l'ancienne) |
| `resources/js/AppPollResults.vue` | SPA résultats (remplace l'ancienne) |
| `resources/js/composables/useFetchApi.js` | Composable fetch API principal (CSRF + JSON) |
| `resources/js/composables/useFetchJson.js` | Utilitaire fetch JSON bas niveau |
| `resources/js/composables/useHashRoute.js` | Routage par hash URL |
| `resources/js/composables/useJsonStorage.js` | Persistance locale JSON |
| `resources/js/utils/fetchJson.js` | Wrapper fetch bas niveau |
| `resources/js/utils/jsonStorage.js` | Abstraction localStorage |
| `resources/js/utils/string.js` | Utilitaires string |
| `README_FRONT.md` | Documentation intégration frontend Vue + Sanctum |

### Frontend — Fichiers supprimés
| Fichier | Raison |
|---|---|
| `resources/js/App.vue` | Remplacé par les SPAs spécialisées |
| `resources/js/app.js` | Remplacé par les entrypoints spécifiques |
| `resources/js/polls/PollBuilderApp.vue` | Déplacé vers `AppPollBuilder.vue` |
| `resources/js/polls/PollResultsApp.vue` | Déplacé vers `AppPollResults.vue` |

---

## 87e51a6 — simple error management in foo test

### Frontend — Fichiers modifiés
| Fichier | Action |
|---|---|
| `resources/js/AppPollBuilder.vue` | Ajout gestion basique des erreurs dans l'exemple de test |

> *Critère PROJET2 #9 : gestion des erreurs utilisateur côté frontend*

---

## 1a99f35 — Add fetchApiToRef example

> Ajout du pattern `fetchApiToRef` et mise en place du composable de polling.

### Backend — Fichiers modifiés
| Fichier | Action |
|---|---|
| `app/Http/Controllers/Api/v1/ApiFooController.php` | Exemples de réponse API enrichis |
| `app/Http/Controllers/Api/v1/ApiPostController.php` | Exemples de réponse API enrichis |
| `app/Http/Controllers/PostController.php` | Ajustements mineurs |

### Frontend — Fichiers créés
| Fichier | Rôle |
|---|---|
| `resources/js/composables/usePolling.js` | Composable polling : `setInterval` + `clearInterval` sur `onUnmounted` |

### Frontend — Fichiers modifiés
| Fichier | Action |
|---|---|
| `resources/js/AppPollBuilder.vue` | Intégration de `fetchApiToRef` |
| `resources/js/composables/useFetchApi.js` | Ajout helper `fetchApiToRef` |

> *Critère PROJET2 #11 : affichage en temps réel via polling*

---

## eafa204 — Add poll migrations and models

> Création des trois modèles Eloquent et de leurs migrations.

### Backend — Fichiers créés
| Fichier | Rôle |
|---|---|
| `app/Models/Poll.php` | Modèle Poll (champs, relations HasMany options/votes) |
| `app/Models/PollOption.php` | Modèle PollOption (BelongsTo Poll) |
| `app/Models/PollVote.php` | Modèle PollVote (BelongsTo Poll, User, PollOption) |
| `database/migrations/2026_04_19_161823_create_polls_table.php` | Table polls : id, user_id, title, question, secret_token, is_draft, allow_multiple_choices, allow_vote_change, results_public, duration, started_at, ends_at |
| `database/migrations/2026_04_19_161825_create_poll_options_table.php` | Table poll_options : id, poll_id, label |
| `database/migrations/2026_04_19_161826_create_poll_votes_table.php` | Table poll_votes : id, poll_id, user_id, poll_option_id |

### Backend — Fichiers modifiés
| Fichier | Action |
|---|---|
| `app/Models/User.php` | Ajout relation `polls()` HasMany |

> *Critère PROJET2 #2/#3/#4 : structure de données pour création, options et paramètres*

---

## 1d8f6d8 — Add show poll example

> Premier endpoint API fonctionnel et exemples de consommation Vue.

### Backend — Fichiers créés
| Fichier | Rôle |
|---|---|
| `app/Http/Controllers/Api/v1/ApiPollController.php` | Premier contrôleur API poll (méthode `show` exemple) |

### Backend — Fichiers modifiés
| Fichier | Action |
|---|---|
| `database/seeders/DatabaseSeeder.php` | Ajout seeds pour polls de test |
| `routes/api.php` | Ajout route `GET /api/v1/polls/{poll}` |

### Frontend — Fichiers modifiés
| Fichier | Action |
|---|---|
| `resources/js/AppPollBuilder.vue` | Consommation de l'endpoint `show` |
| `resources/js/composables/useFetchApi.js` | Amélioration gestion erreurs |
| `resources/js/composables/useFetchJson.js` | Amélioration gestion erreurs |

> *Critère PROJET2 #8 : consommation correcte des endpoints JSON*

---

## 8e938a6 — Add Vue poll dashboards

> Refonte majeure de l'architecture Vue. Mise en place du dashboard et du composant liste.

### Backend — Fichiers créés
| Fichier | Rôle |
|---|---|
| `app/Http/Controllers/PollDashboardController.php` | Contrôleur web servant les vues Blade polls |
| `resources/views/components/vue-app-layout.blade.php` | Layout Blade générique pour les SPAs Vue |
| `resources/views/polls/dashboard.blade.php` | Vue Blade dashboard (charge `poll-dashboard.js`) |

### Backend — Fichiers modifiés
| Fichier | Action |
|---|---|
| `routes/web.php` | Ajout route `GET /polls/dashboard` |
| `vite.config.js` | Ajout entrypoint `poll-dashboard.js` |
| `README_FRONT.md` | Mise à jour documentation frontend |

### Frontend — Fichiers créés
| Fichier | Rôle |
|---|---|
| `resources/js/AppPollDashboard.vue` | SPA principale (remplace AppPollBuilder) |
| `resources/js/components/PollTable.vue` | Composant liste des sondages |
| `resources/js/poll-dashboard-integrated.js` | Entrypoint dashboard intégré (temporaire) |
| `resources/js/AppPollDashboardIntegrated.vue` | Version intégrée temporaire |

### Frontend — Fichiers supprimés / renommés
| Fichier | Action |
|---|---|
| `resources/js/AppPollBuilder.vue` → `AppPollDashboardIntegrated.vue` | Renommage |
| `resources/js/AppPollResults.vue` | Supprimé (intégré dans la SPA dashboard) |
| `resources/js/poll-builder.js` → `poll-dashboard.js` | Renommé |
| `resources/js/poll-results.js` | Supprimé |
| `resources/views/polls/builder.blade.php` → `dashboard-integrated.blade.php` | Renommé |
| `resources/views/polls/results.blade.php` | Supprimé |

> *Critère PROJET2 #1 : dashboard des sondages de la personne connectée*

---

## a11a66c — Update README.md

### Docs
| Fichier | Action |
|---|---|
| `README.md` | Mise à jour documentation générale |

---

## 38fce94 / 6f066ac / 7b26ad7 / f8cb3be — TP.md updates

### Docs
| Fichier | Action |
|---|---|
| `TP.md` | Création puis mises à jour successives du cahier des charges TP |

---

## 93cde12 — Start dashboard example for poll management

> Nettoyage : suppression des fichiers "integrated" temporaires, consolidation sur une seule SPA.

### Backend — Fichiers modifiés
| Fichier | Action |
|---|---|
| `app/Http/Controllers/Api/v1/ApiPollController.php` | Ajout méthode `index` (liste polls du user) |
| `routes/api.php` | Ajout `GET /api/v1/polls` |
| `routes/web.php` | Nettoyage routes |

### Frontend — Fichiers modifiés
| Fichier | Action |
|---|---|
| `resources/js/AppPollDashboard.vue` | Consommation `GET /api/v1/polls`, affichage liste |
| `resources/js/components/PollTable.vue` | Amélioration affichage liste |
| `resources/js/poll-dashboard.js` | Nettoyage et consolidation entrypoint |
| `resources/views/polls/dashboard.blade.php` | Mise à jour layout |

### Frontend — Fichiers supprimés
| Fichier | Raison |
|---|---|
| `resources/js/AppPollDashboardIntegrated.vue` | Version temporaire supprimée |
| `resources/js/poll-dashboard-integrated.js` | Entrypoint temporaire supprimé |
| `resources/views/polls/dashboard-integrated.blade.php` | Vue temporaire supprimée |

---

## 1ffc5f0 — Add delete feature

> Ajout de la fonctionnalité de suppression et introduction du store.

### Backend — Fichiers modifiés
| Fichier | Action |
|---|---|
| `app/Http/Controllers/Api/v1/ApiPollController.php` | Ajout méthode `destroy` (DELETE) |
| `routes/api.php` | Ajout `DELETE /api/v1/polls/{poll}` |

### Frontend — Fichiers créés
| Fichier | Rôle |
|---|---|
| `resources/js/stores/usePollStore.js` | Store réactif pour la liste des polls (remplacé par composables dans V1) |

### Frontend — Fichiers modifiés
| Fichier | Action |
|---|---|
| `resources/js/AppPollDashboard.vue` | Intégration suppression depuis le dashboard |
| `resources/js/components/PollTable.vue` | Ajout bouton Supprimer, émission event `@delete` |
| `resources/views/polls/dashboard.blade.php` | Ajustements layout |

> *Critère PROJET2 #2 : suppression d'un sondage depuis le frontend*

---

## 4261e7f — Add delete example

### Backend — Fichiers modifiés
| Fichier | Action |
|---|---|
| `app/Http/Controllers/Api/v1/ApiPollController.php` | Amélioration méthode `destroy` (vérification propriétaire) |
| `database/seeders/DatabaseSeeder.php` | Mise à jour seeds |
| `routes/web.php` | Ajout routes complémentaires |

---

## 7a2c290 — Remove dashboard integrated

### Frontend — Fichiers modifiés
| Fichier | Action |
|---|---|
| `vite.config.js` | Suppression entrypoint `poll-dashboard-integrated` |

---

## e94db7f — Update README.md

### Docs
| Fichier | Action |
|---|---|
| `README.md` | Mise à jour documentation installation |

---

## cf6623b / 46c64f3 / c17539e — Create PROJET2.md + updates

### Docs
| Fichier | Action |
|---|---|
| `PROJET2.md` | Création du document d'architecture, plan de développement, roadmap, critères, CHANGELOG et manifest IA |

---

## 8d33c25 — V1 _(implémentation principale)_

> **Commit principal.** Implémentation complète de toutes les fonctionnalités du module poll.

### Backend — Fichiers créés
| Fichier | Rôle | Critère PROJET2 |
|---|---|---|
| `app/Http/Requests/StorePollRequest.php` | Validation création : question, options (min 2), booléens, duration | #9 |
| `app/Http/Requests/UpdatePollRequest.php` | Même règles en `sometimes` (champs optionnels à l'édition) | #9 |
| `app/Http/Requests/StorePollVoteRequest.php` | Validation vote : `option_ids[]` avec vérification existence en base | #6/#9 |
| `database/migrations/2026_05_14_141349_add_color_to_polls_table.php` | Ajout colonne `color` sur la table polls (palette visuelle) | #10 |
| `resources/views/polls/vote.blade.php` | Vue Blade pour le lien de partage, charge `poll-dashboard.js` avec `token` en prop `data-*` | #5 |
| `public/favicon.png` | Favicon personnalisé | #10 |
| `public/logo.png` | Logo du projet | #10 |

### Backend — Fichiers modifiés
| Fichier | Action | Critère PROJET2 |
|---|---|---|
| `app/Models/Poll.php` | Ajout `$fillable` complet, `$casts` (booléens + dates), helpers `isExpired()` et `isActive()` | #2/#4/#7 |
| `app/Models/PollOption.php` | Ajout `$fillable = ['poll_id', 'label']` | #3 |
| `app/Models/PollVote.php` | Ajout `$fillable = ['poll_id', 'user_id', 'poll_option_id']` | #6 |
| `app/Http/Controllers/Api/v1/ApiPollController.php` | Réécriture complète des 8 méthodes : `index`, `store`, `show`, `update`, `destroy`, `showByToken`, `vote`, `results` avec toutes les règles métier | #8 |
| `app/Http/Controllers/PollDashboardController.php` | Passage du token à la vue Blade `vote.blade.php` | #5 |
| `routes/api.php` | Ajout des 8 endpoints `/api/v1/polls/...` (routes token déclarées **avant** `/{poll}` pour éviter le conflit de routing Laravel) | #8 |
| `routes/web.php` | Ajout `GET /polls/vote/{token}` → vue `polls.vote` | #5 |
| `resources/views/polls/dashboard.blade.php` | Mise à jour layout dashboard | #1 |
| `resources/views/components/vue-app-layout.blade.php` | Mise à jour layout générique Vue | #10 |
| `PROJET2.md` | Ajout section CHANGELOG | — |

### Frontend — Fichiers créés
| Fichier | Rôle | Critère PROJET2 |
|---|---|---|
| `resources/js/composables/usePolls.js` | CRUD sondages : `fetchPolls`, `createPoll`, `updatePoll`, `deletePoll`, mise à jour liste locale | #2/#8/#14 |
| `resources/js/composables/usePollVoting.js` | Chargement sondage par token, gestion `selectedOptions`, `submitVote`, `toggleOption` (radio/checkbox logic) | #6/#8/#14 |
| `resources/js/composables/usePollResults.js` | `fetchResults(token)`, utilisé avec `usePolling` pour le refresh automatique | #11/#14 |
| `resources/js/components/PollEditor.vue` | Formulaire création/édition : intègre `PollOptionsEditor` + `PollSettings` + `PollShareLink`, validation front, émission `@saved`/`@cancel` | #2/#3/#4/#14 |
| `resources/js/components/PollOptionsEditor.vue` | Ajout/modif/suppression options (compatible `v-model`, min 2 options imposé) | #3/#14 |
| `resources/js/components/PollSettings.vue` | Toggles : choix multiple, résultats publics, modifier vote ; input durée en minutes ; bouton Lancer (is_draft → false) | #4/#14 |
| `resources/js/components/PollShareLink.vue` | Affiche l'URL de partage construite depuis `secret_token`, bouton Copier (`navigator.clipboard`) | #5/#14 |
| `resources/js/components/PollVote.vue` | Interface vote : radios/checkboxes selon `allow_multiple_choices`, états expiré/déjà voté, erreurs API | #6/#7/#9/#14 |
| `resources/js/components/PollResults.vue` | Résultats avec barres CSS animées + pourcentages, polling automatique toutes les 5 s, badge Live, arrêt sur `onUnmounted` | #11/#14 |
| `resources/js/components/PollGrid.vue` | Composant grille / carte alternative pour l'affichage des sondages | #1/#10 |
| `resources/js/utils/pollColors.js` | Utilitaire couleurs pastel pour les sondages (palette visuelle) | #10 |

### Frontend — Fichiers modifiés
| Fichier | Action | Critère PROJET2 |
|---|---|---|
| `resources/js/AppPollDashboard.vue` | Réécriture complète : SPA router (`view` ref), détection token via `data-token`, navigation list/create/edit/vote/results, glassmorphisme | #1/#7/#12/#14 |
| `resources/js/components/PollTable.vue` | Réécriture : cards avec badge statut (Brouillon/Actif/Terminé), couleur sondage, actions éditer/supprimer/résultats, lien partage intégré | #1/#5/#10 |

### Règles métier implémentées côté API
| Règle | Implémentation | Critère PROJET2 |
|---|---|---|
| Propriétaire | Seul le créateur peut modifier/supprimer (403 sinon) | #8 |
| Brouillon | Aucun vote accepté si `is_draft = true` | #7 |
| Expiré | Aucun vote accepté si `ends_at` est dépassé | #7 |
| Choix unique | Un seul `option_id` autorisé ; un seul vote par user par sondage | #6 |
| Vote modifiable | Suppression de l'ancien vote si `allow_vote_change = true` | Bonus |
| Résultats | Visibles uniquement si `results_public = true` ou si propriétaire | #7 |

---

## 6e7bdc9 — V1 final _(finition)_

> Corrections et améliorations de l'interface après première version complète.

### Frontend — Fichiers modifiés
| Fichier | Action | Critère PROJET2 |
|---|---|---|
| `resources/js/AppPollDashboard.vue` | Corrections navigation, gestion du token en mode vote, passage des props corrects aux composants enfants | #7/#12 |
| `resources/js/components/PollEditor.vue` | Corrections formulaire, réinitialisation état, gestion erreurs API 422 | #9/#12 |
| `resources/js/components/PollGrid.vue` | Améliorations visuelles grille sondages | #10 |
| `resources/js/components/PollResults.vue` | Améliorations barres CSS, affichage total votes, gestion cas 0 vote | #11 |
| `resources/js/components/PollSettings.vue` | Correction conversion durée minutes/secondes, affichage date de fin calculée | #4 |

---

## Récapitulatif par critère PROJET2

| Critère | Description | Commits clés |
|---|---|---|
| **#1** | Dashboard des sondages de la personne connectée | `8e938a6`, `93cde12`, `8d33c25` |
| **#2** | Création, édition et suppression depuis le frontend | `1ffc5f0`, `4261e7f`, `8d33c25` |
| **#3** | Gestion des options (ajout, modif, suppression) | `eafa204`, `8d33c25` |
| **#4** | Gestion des paramètres (brouillon, multi, public, durée) | `eafa204`, `8d33c25`, `6e7bdc9` |
| **#5** | Lien de partage token + page accessible via token | `8d33c25` |
| **#6** | Vote valide, unicité choix unique garantie front + API | `8d33c25` |
| **#7** | Affichage conditionnel (état, date fin, droits, résultats publics) | `8d33c25`, `6e7bdc9` |
| **#8** | Consommation correcte des endpoints JSON | `1d8f6d8`, `8d33c25` |
| **#9** | Gestion des erreurs utilisateur côté frontend | `87e51a6`, `8d33c25`, `6e7bdc9` |
| **#10** | Interface lisible, responsive, agréable | `8d33c25`, `6e7bdc9` |
| **#11** | Polling temps réel + aperçu graphique | `1a99f35`, `8d33c25`, `6e7bdc9` |
| **#12** | Projet fonctionnel de bout en bout | `8d33c25`, `6e7bdc9` |
| **#13** | Code lisible, README, contrôle de version | tous commits |
| **#14** | Bons composants Vue, composables, architecture cohérente | `8d33c25` |
| **#15** | Nommage, lisibilité, organisation | `8d33c25`, `6e7bdc9` |
| **Bonus** | Changement de vote (`allow_vote_change`) | `8d33c25` |
