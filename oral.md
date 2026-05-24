# Oral - Application de sondage Laravel + Vue

Ce fichier sert de fiche de préparation pour l'oral. Le but n'est pas
d'apprendre tout par coeur, mais de savoir expliquer simplement ce que fait
l'application, comment elle est organisée, et pourquoi les choix techniques ont
été faits.

## 0. Ce qu'on attend de toi a l'oral

L'oral ne sert pas seulement a montrer que l'application marche. Le prof veut
surtout verifier que tu comprends ton projet.

Concretement, tu dois montrer que tu sais :

- faire une demo claire de l'application ;
- expliquer l'architecture Laravel + Vue ;
- expliquer comment le frontend parle avec le backend ;
- retrouver rapidement les fichiers importants ;
- expliquer les choix techniques principaux ;
- expliquer les bases de Vue utilisees dans ton code ;
- expliquer les controles cote API : validation, droits, vote unique, expiration ;
- modifier un petit truc en direct si le prof demande.

### Points importants de l'evaluation

Il y a deux parties dans l'evaluation :

1. Le rendu du projet :
   - dashboard des sondages ;
   - creation, edition, suppression ;
   - options du sondage ;
   - parametres ;
   - lien de partage avec token ;
   - vote ;
   - resultats ;
   - polling ;
   - interface claire ;
   - code structure ;
   - README et organisation.

2. La presentation orale :
   - informations claires ;
   - reponses pertinentes ;
   - capacite a modifier le code en direct ;
   - comprehension de Vue, du backend et des echanges API ;
   - preuve que tu maitrises le code, meme si tu as utilise de l'IA.

La note finale suit la formule donnee par la consigne :

```text
(points obtenus / points maximum) x 5 + 1
```

Donc l'objectif de l'oral n'est pas de faire semblant d'avoir tout code seul.
L'objectif est de prouver que tu comprends le code rendu et que tu peux le
defendre.

Phrase utile si on parle d'IA :

> J'ai utilise de l'aide, mais j'ai repris le code pour comprendre la structure. Je peux expliquer les composants principaux, les routes API et les choix de validation.

## 1. Objectif de l'application

Mon application permet de créer et partager des sondages.

Un utilisateur connecté peut :

- voir ses sondages dans un dashboard ;
- créer un nouveau sondage ;
- modifier ou supprimer ses sondages ;
- ajouter plusieurs options de réponse ;
- choisir les paramètres du sondage ;
- lancer le sondage directement ou le garder en brouillon ;
- copier un lien de partage contenant un token ;
- consulter les résultats.

Une personne qui reçoit le lien peut :

- ouvrir le sondage grâce au token dans l'URL ;
- voter si elle est connectée ;
- voir les résultats uniquement si les résultats sont publics ou si elle est propriétaire du sondage ;
- voir les résultats se mettre à jour régulièrement grâce au polling.

## 2. Plan conseillé pour les 10 minutes

### 0:00 - 0:30 : Introduction

Dire simplement :

> J'ai développé une application de sondage avec Laravel côté backend et Vue 3 côté frontend. Laravel expose une API JSON versionnée, et Vue consomme cette API pour gérer le dashboard, la création, le vote et les résultats.

### 0:30 - 5:00 : Démo rapide

Ordre conseillé pour la démo :

1. Ouvrir le dashboard :
   - route : `/polls/dashboard`
   - montrer la liste des sondages de l'utilisateur connecté.

2. Créer un sondage :
   - entrer une question ;
   - ajouter au moins deux options ;
   - choisir une couleur ;
   - configurer les paramètres :
     - choix simple ou multiple ;
     - résultats publics ou privés ;
     - modification du vote autorisée ou non ;
     - durée ;
     - brouillon ou lancement direct.

3. Modifier un sondage :
   - changer la question ou une option ;
   - expliquer que le frontend envoie un `PATCH` à l'API.

4. Copier ou montrer le lien de partage :
   - le lien contient le `secret_token` ;
   - exemple : `/polls/vote/{token}`.

5. Voter :
   - ouvrir la page de vote ;
   - montrer la différence entre choix unique et choix multiple ;
   - envoyer le vote.

6. Montrer les résultats :
   - les résultats affichent le nombre de votes et les pourcentages ;
   - le graphique est fait avec des barres CSS ;
   - les données sont rechargées toutes les 5 secondes.

7. Montrer un cas limite :
   - sondage expiré : le vote est bloqué ;
   - résultats privés : visibles seulement par le propriétaire ;
   - sondage déjà voté : vote bloqué sauf si la modification est autorisée.

### 5:00 - 8:00 : Architecture

Expliquer la séparation :

- Laravel gère les routes, la sécurité, la validation et la base de données.
- Vue gère l'interface, les interactions utilisateur et les appels API.
- Les données passent en JSON entre Vue et Laravel.

### 8:00 - 10:00 : Points techniques importants

Parler surtout de :

- la réactivité Vue avec `ref` et `computed` ;
- les composants Vue ;
- les composables ;
- les routes API ;
- la validation côté backend ;
- la protection des droits côté backend ;
- l'unicité du vote pour les sondages à choix unique ;
- le polling des résultats.

## 3. Structure du projet à connaître

Voici les fichiers importants, avec indentation pour expliquer l'organisation.

```text
app/
  Http/
    Controllers/
      Api/
        v1/
          ApiPollController.php
            Controleur principal de l'API des sondages.
            Il contient :
              - index() : liste des sondages du user connecté
              - store() : création
              - show() : détail propriétaire
              - update() : modification
              - destroy() : suppression
              - showByToken() : affichage public via token
              - vote() : vote d'un utilisateur connecté
              - results() : résultats du sondage

    Requests/
      StorePollRequest.php
        Validation de la création d'un sondage.

      UpdatePollRequest.php
        Validation de la modification d'un sondage.

      StorePollVoteRequest.php
        Validation de la soumission d'un vote.

  Models/
    Poll.php
      Modele principal du sondage.
      Relations :
        - user()
        - options()
        - votes()
      Methodes utiles :
        - isExpired()
        - isActive()

    PollOption.php
      Option de réponse d'un sondage.

    PollVote.php
      Vote d'un utilisateur pour une option.

routes/
  web.php
    Routes HTML Laravel.
    Important :
      - /polls/dashboard
      - /polls/vote/{token}

  api.php
    Routes JSON.
    Important :
      - /api/v1/polls
      - /api/v1/polls/{poll}
      - /api/v1/polls/token/{token}
      - /api/v1/polls/token/{token}/vote
      - /api/v1/polls/token/{token}/results

resources/
  views/
    polls/
      dashboard.blade.php
        Page Blade qui charge l'app Vue du dashboard.

      vote.blade.php
        Page Blade qui charge l'app Vue de vote avec le token.

  js/
    poll-dashboard.js
      Point d'entree Vue.
      Il monte AppPollDashboard.vue dans la page Blade.

    AppPollDashboard.vue
      Composant principal.
      Il choisit la vue active :
        - list
        - create
        - edit
        - vote
        - results

    components/
      PollGrid.vue
        Affichage de la liste des sondages.

      PollEditor.vue
        Formulaire de creation et modification.

      PollOptionsEditor.vue
        Gestion des options de réponse.

      PollSettings.vue
        Gestion des paramètres :
          - choix multiple
          - résultats publics
          - modification du vote
          - durée
          - brouillon / lancé

      PollShareLink.vue
        Affiche le lien de partage avec le token.

      PollVote.vue
        Page de vote.
        Elle charge le sondage via token et soumet le vote.

      PollResults.vue
        Affichage des résultats avec polling et graphique.

    composables/
      usePolls.js
        CRUD des sondages :
          - fetchPolls()
          - createPoll()
          - updatePoll()
          - deletePoll()

      usePollVoting.js
        Logique de vote :
          - charger un sondage par token
          - sélectionner les options
          - envoyer le vote

      usePollResults.js
        Récupération des résultats.

      usePolling.js
        Relance une fonction toutes les X millisecondes.
        Utilisé pour rafraîchir les résultats.

      useFetchApi.js
        Wrapper autour de fetch().
        Centralise les appels JSON vers l'API.

    utils/
      pollColors.js
        Couleurs disponibles pour les sondages.

database/
  migrations/
    create_polls_table.php
      Table des sondages.

    create_poll_options_table.php
      Table des options.

    create_poll_votes_table.php
      Table des votes.
```

## 3bis. Les fondamentaux a vraiment comprendre

Cette partie est importante si tu connais encore mal le code. Le but est de
savoir expliquer les patterns qui reviennent dans ton projet.

### Pattern 1 : une page Blade monte une app Vue

Laravel affiche d'abord une page Blade. Dans cette page, il y a une div :

```html
<div id="app" data-props='...'></div>
```

Ensuite, Vue est charge avec Vite :

```php
@vite(['resources/js/poll-dashboard.js'])
```

Puis dans `resources/js/poll-dashboard.js` :

```js
import { createApp } from 'vue';
import App from './AppPollDashboard.vue';

const el = document.getElementById('app');
const props = JSON.parse(el.dataset.props ?? '{}');

createApp(App, props).mount(el);
```

Explication simple :

> Laravel sert la page HTML, puis Vue prend le controle de la div `#app`. Les donnees initiales comme le token sont passees par `data-props`.

Exemple dans ton projet :

- `resources/views/polls/dashboard.blade.php` charge l'app du dashboard ;
- `resources/views/polls/vote.blade.php` charge l'app de vote avec le token ;
- `resources/js/poll-dashboard.js` monte `AppPollDashboard.vue`.

### Pattern 2 : le composant principal choisit l'ecran actif

Dans `AppPollDashboard.vue`, tu n'utilises pas Vue Router. Tu geres une petite
navigation interne avec une variable reactive :

```js
const view = ref(props.token ? 'vote' : 'list');
```

Puis dans le template :

```vue
<PollGrid v-if="view === 'list'" />
<PollEditor v-else-if="view === 'create' || view === 'edit'" />
<PollVote v-else-if="view === 'vote'" />
<PollResults v-else-if="view === 'results'" />
```

Explication simple :

> J'ai une SPA simple. Au lieu d'ajouter Vue Router, je change une variable `view`. Selon sa valeur, Vue affiche le dashboard, l'editeur, le vote ou les resultats.

Pourquoi c'est defendable :

- l'app est assez petite ;
- les vues sont peu nombreuses ;
- c'est simple a comprendre ;
- les routes importantes restent cote Laravel.

### Pattern 3 : parent vers enfant avec `props`

Une `prop`, c'est une donnee envoyee par un composant parent a un composant
enfant.

Exemple :

```vue
<PollVote :token="activeToken" />
```

Ici :

- `AppPollDashboard.vue` est le parent ;
- `PollVote.vue` est l'enfant ;
- `activeToken` est envoye a l'enfant sous le nom `token`.

Dans `PollVote.vue` :

```js
const props = defineProps({
    token: { type: String, required: true },
});
```

Phrase utile :

> Les props servent a descendre les donnees du parent vers l'enfant.

### Pattern 4 : enfant vers parent avec `emit`

Un `emit`, c'est un evenement envoye par l'enfant au parent.

Exemple :

```vue
<PollEditor @saved="onSaved" />
```

Dans `PollEditor.vue`, apres la sauvegarde :

```js
emit('saved', result);
```

Dans le parent :

```js
function onSaved() {
    fetchPolls();
    backToList();
}
```

Explication simple :

> Quand l'enfant a fini son travail, il previent le parent. Ici, quand le sondage est sauvegarde, le parent recharge la liste et revient au dashboard.

### Pattern 5 : `v-model` personnalise

Dans ton projet, `PollSettings.vue` utilise un `v-model` sur un objet :

```vue
<PollSettings v-model="settings" />
```

Dans l'enfant, la valeur arrive dans `modelValue` :

```js
const props = defineProps({
    modelValue: Object,
});
```

Pour modifier la valeur, l'enfant fait :

```js
emit('update:modelValue', { ...props.modelValue, [key]: value });
```

Pourquoi on copie avec `{ ...props.modelValue }` ?

> Pour creer un nouvel objet avec le champ modifie, au lieu de modifier directement la prop. En Vue, un enfant ne doit pas modifier directement ses props.

Phrase utile :

> `v-model` est pratique quand un composant enfant edite une valeur du parent, comme les parametres du sondage.

### Pattern 6 : `ref` pour les valeurs qui changent

Dans `usePolls.js` :

```js
const polls = ref([]);
const loading = ref(false);
const error = ref(null);
```

Ces valeurs changent pendant la vie de l'application.

Exemple :

- avant l'appel API : `loading.value = true` ;
- apres l'appel API : `polls.value = await fetchApi(...)` ;
- si erreur : `error.value = ...`.

Phrase utile :

> J'utilise `ref` pour les donnees qui doivent etre reactives. Quand la valeur change, Vue met a jour l'affichage.

### Pattern 7 : `computed` pour les valeurs derivees

Dans `PollEditor.vue` :

```js
const isEditing = computed(() => props.poll !== null);
```

Cette valeur depend de `props.poll`.

Si `props.poll` existe :

- on est en edition ;
- le bouton affiche `Enregistrer`.

Sinon :

- on est en creation ;
- le bouton affiche `Creer`.

Phrase utile :

> Une `computed` sert a calculer une valeur a partir d'autres valeurs reactives. Je ne la modifie pas directement.

### Pattern 8 : composable pour sortir la logique du composant

Sans composable, `AppPollDashboard.vue` aurait trop de code :

- appels API ;
- gestion loading ;
- gestion erreur ;
- creation ;
- modification ;
- suppression.

Donc cette logique est dans `usePolls.js` :

```js
export function usePolls() {
    const polls = ref([]);
    const loading = ref(false);
    const error = ref(null);

    async function fetchPolls() { ... }
    async function createPoll(data) { ... }
    async function updatePoll(id, data) { ... }
    async function deletePoll(id) { ... }

    return { polls, loading, error, fetchPolls, createPoll, updatePoll, deletePoll };
}
```

Dans le composant :

```js
const { polls, loading, error, fetchPolls, deletePoll, updatePoll } = usePolls();
```

Explication simple :

> Un composable est une fonction qui regroupe de la logique reactive reutilisable. Ca rend les composants plus propres.

### Pattern 9 : wrapper `fetchApi`

Au lieu d'ecrire `fetch()` partout, tu as `useFetchApi.js`.

Il centralise :

- l'URL de base ;
- les headers JSON ;
- la methode HTTP ;
- la conversion en JSON ;
- la gestion d'erreur ;
- le timeout.

Exemple d'utilisation :

```js
await fetchApi({ url: '/polls' });
await fetchApi({ url: `/polls/${id}`, data, method: 'PATCH' });
await fetchApi({ url: `/polls/token/${token}/vote`, data: { option_ids } });
```

Phrase utile :

> J'ai un wrapper autour de `fetch` pour eviter de repeter la meme logique dans tous les composants.

### Pattern 10 : backend = vraie source de securite

Le frontend peut cacher des boutons, mais il ne protege pas vraiment.

Exemple :

Dans `ApiPollController.php`, avant de modifier :

```php
if ($poll->user_id !== $request->user()->id) {
    return response()->json(['message' => 'Acces refuse.'], 403);
}
```

Explication simple :

> Meme si quelqu'un appelle l'API manuellement, Laravel verifie que le sondage appartient bien a l'utilisateur connecte.

### Pattern 11 : validation avec FormRequest

Les fichiers de validation sont separes :

- `StorePollRequest.php` pour creer ;
- `UpdatePollRequest.php` pour modifier ;
- `StorePollVoteRequest.php` pour voter.

Exemple :

```php
'question' => ['required', 'string', 'max:255'],
'options' => ['required', 'array', 'min:2'],
'duration' => ['nullable', 'integer', 'min:60'],
```

Phrase utile :

> Les `FormRequest` permettent de sortir la validation du controleur. Le controleur reste concentre sur la logique metier.

### Pattern 12 : relations Eloquent

Dans `Poll.php` :

```php
public function user()
{
    return $this->belongsTo(User::class);
}

public function options()
{
    return $this->hasMany(PollOption::class);
}

public function votes()
{
    return $this->hasMany(PollVote::class);
}
```

Explication simple :

> Un sondage appartient a un utilisateur, possede plusieurs options et possede plusieurs votes.

Quand le controleur fait :

```php
$poll->load('options');
```

ca charge aussi les options du sondage.

Quand il fait :

```php
$q->withCount('votes');
```

ca ajoute le nombre de votes par option.

### Pattern 13 : synchronisation des options

Quand on modifie un sondage, les options peuvent avoir change.

Dans `update()` :

- si une option a un `id`, on la met a jour ;
- si elle n'a pas d'`id`, on la cree ;
- les anciennes options qui ne sont plus envoyees sont supprimees.

Phrase utile :

> J'ai une logique de synchronisation : le frontend envoie l'etat actuel des options, et le backend met la base de donnees en accord avec cet etat.

### Pattern 14 : token de partage

A la creation :

```php
$poll->secret_token = Str::random(32);
```

Le token sert a creer un lien du type :

```text
/polls/vote/{token}
```

Puis Vue appelle :

```text
/api/v1/polls/token/{token}
```

Phrase utile :

> Le token permet d'ouvrir un sondage sans connaitre son id interne. C'est mieux pour le partage.

### Pattern 15 : polling

Dans `PollResults.vue` :

```js
usePolling(() => fetchResults(props.token), 5000);
```

Dans `usePolling.js` :

```js
onMounted(() => {
    timer = setInterval(fn, interval);
});

onUnmounted(() => clearInterval(timer));
```

Explication simple :

> Quand le composant resultats est affiche, il lance un intervalle. Toutes les 5 secondes, il recharge les resultats. Quand le composant disparait, il arrete l'intervalle.

## 3ter. Parcours complet d'une action utilisateur

### Exemple 1 : creer un sondage

```text
Utilisateur remplit le formulaire
  -> PollEditor.vue garde les valeurs dans des ref
  -> submit() construit un payload
  -> usePolls.createPoll(payload)
  -> useFetchApi envoie POST /api/v1/polls
  -> routes/api.php envoie vers ApiPollController@store
  -> StorePollRequest valide les donnees
  -> Laravel cree le Poll + les PollOption
  -> Laravel retourne du JSON
  -> Vue ajoute le sondage dans polls.value
  -> le dashboard se met a jour
```

Phrase a dire :

> L'action part du composant Vue, passe par un composable, arrive sur une route API Laravel, puis revient en JSON pour mettre a jour l'interface.

### Exemple 2 : voter

```text
Utilisateur ouvre /polls/vote/{token}
  -> vote.blade.php passe le token a Vue
  -> AppPollDashboard.vue affiche PollVote.vue
  -> PollVote.vue appelle fetchPollByToken(token)
  -> GET /api/v1/polls/token/{token}
  -> Laravel retourne le sondage, les options, hasVoted, expired
  -> l'utilisateur selectionne une ou plusieurs options
  -> submitVote(token)
  -> POST /api/v1/polls/token/{token}/vote
  -> StorePollVoteRequest valide option_ids
  -> ApiPollController verifie brouillon, expiration, options valides, vote unique
  -> PollVote est cree en base
  -> Vue affiche les resultats
```

### Exemple 3 : voir les resultats

```text
PollResults.vue est affiche
  -> fetchResults(token)
  -> GET /api/v1/polls/token/{token}/results
  -> Laravel verifie results_public ou proprietaire
  -> Laravel compte les votes avec withCount('votes')
  -> Laravel calcule les pourcentages
  -> Vue affiche les barres CSS
  -> usePolling recommence toutes les 5 secondes
```

## 4. Ce qu'il faut savoir expliquer simplement

### Laravel

Laravel sert à :

- définir les routes web et API ;
- valider les données reçues ;
- lire et écrire en base avec Eloquent ;
- vérifier les droits d'accès ;
- renvoyer des réponses JSON au frontend.

Exemple à dire :

> Quand Vue crée un sondage, il envoie une requête POST vers `/api/v1/polls`. Laravel valide les données avec `StorePollRequest`, crée le sondage, crée ses options, puis retourne le sondage en JSON.

### Vue

Vue sert à :

- afficher les écrans ;
- garder l'état de l'interface ;
- réagir aux actions utilisateur ;
- appeler l'API Laravel avec `fetch`.

Exemple à dire :

> J'ai découpé l'interface en composants. Le composant principal choisit la vue active, et les composants spécialisés gèrent chacun une partie : édition, options, paramètres, vote ou résultats.

### Composants

Un composant Vue est une partie réutilisable de l'interface.

Dans mon projet :

- `PollEditor.vue` gère le formulaire du sondage ;
- `PollSettings.vue` gère les paramètres ;
- `PollVote.vue` gère la page de vote ;
- `PollResults.vue` gère les résultats.

### Composables

Un composable est une fonction qui regroupe de la logique réutilisable.

Dans mon projet :

- `usePolls()` évite de mettre tout le CRUD dans les composants ;
- `usePollVoting()` regroupe la logique de vote ;
- `usePolling()` évite de réécrire le `setInterval` dans le composant ;
- `useFetchApi()` centralise les appels API.

Phrase utile :

> J'ai utilisé des composables pour séparer la logique métier de l'affichage. Les composants restent plus lisibles, et les appels API sont regroupés au même endroit.

## 5. Réactivité Vue à connaître

### `ref`

`ref` crée une valeur réactive.

Exemple dans le projet :

```js
const polls = ref([]);
const loading = ref(false);
const error = ref(null);
```

Explication :

> Quand `polls.value` change, Vue met automatiquement l'affichage à jour.

### `computed`

`computed` sert à calculer une valeur à partir d'autres valeurs réactives.

Exemple dans le projet :

```js
const isEditing = computed(() => props.poll !== null);
```

Explication :

> `isEditing` se recalcule automatiquement quand `props.poll` change. C'est utile pour éviter de dupliquer de la logique dans le template.

### `props`

Les `props` servent à passer des données d'un parent vers un enfant.

Exemple :

```vue
<PollVote :token="activeToken" />
```

### `emit`

`emit` sert à envoyer un événement d'un enfant vers son parent.

Exemple :

```vue
<PollEditor @saved="onSaved" />
```

Explication :

> Quand le formulaire est sauvegardé, l'enfant émet `saved`, et le parent recharge la liste puis revient au dashboard.

### `v-model`

`v-model` permet de lier une valeur entre parent et enfant.

Exemple :

```vue
<PollSettings v-model="settings" />
```

Explication :

> Le composant `PollSettings` modifie l'objet `settings` en envoyant `update:modelValue`.

## 6. Endpoints API à maîtriser

```text
GET /api/v1/polls
  Retourne les sondages de l'utilisateur connecté.

POST /api/v1/polls
  Crée un sondage avec ses options.

GET /api/v1/polls/{poll}
  Retourne le détail d'un sondage.
  Accessible seulement au propriétaire.

PATCH /api/v1/polls/{poll}
  Modifie un sondage et synchronise ses options.

DELETE /api/v1/polls/{poll}
  Supprime un sondage.

GET /api/v1/polls/token/{token}
  Retourne un sondage publié à partir du token.

POST /api/v1/polls/token/{token}/vote
  Enregistre le vote d'un utilisateur connecté.

GET /api/v1/polls/token/{token}/results
  Retourne les résultats si l'utilisateur a le droit de les voir.
```

Important :

- les routes CRUD sont protégées par `auth:sanctum` ;
- le vote est aussi protégé par `auth:sanctum` ;
- l'affichage par token peut être public ;
- les résultats sont filtrés selon `results_public` ou propriétaire.

## 7. Sécurité et droits d'accès

Points importants à dire :

- Un utilisateur ne peut modifier ou supprimer que ses propres sondages.
- Le backend vérifie le propriétaire avec `user_id`.
- Un sondage en brouillon n'est pas disponible via le lien public.
- Un sondage expiré ne peut plus recevoir de vote.
- Les résultats privés ne sont visibles que par le propriétaire.
- Le frontend améliore l'expérience utilisateur, mais la vraie sécurité est côté Laravel.

Phrase utile :

> Même si quelqu'un essaie d'appeler directement l'API, Laravel revérifie les droits. Je ne me base pas seulement sur le frontend.

## 8. Vote unique et choix multiple

Il y a deux protections.

Côté frontend :

- dans `usePollVoting.js`, si le sondage est à choix unique, `toggleOption()` remplace le choix précédent ;
- si le sondage accepte plusieurs choix, la fonction ajoute ou retire l'option du tableau.

Côté backend :

- `StorePollVoteRequest` valide que `option_ids` est un tableau ;
- `ApiPollController::vote()` vérifie que les options appartiennent au sondage ;
- si `allow_multiple_choices` est faux, l'API refuse plusieurs options ;
- si l'utilisateur a déjà voté et que `allow_vote_change` est faux, l'API refuse un deuxième vote ;
- si `allow_vote_change` est vrai, l'ancien vote est supprimé puis remplacé.

Phrase utile :

> Le frontend empêche l'erreur dans l'interface, mais l'API garantit vraiment la règle.

## 9. Polling des résultats

Le polling sert à rafraîchir les résultats automatiquement.

Dans le projet :

- `PollResults.vue` affiche les résultats ;
- `usePollResults.js` récupère les données ;
- `usePolling.js` appelle `fetchResults()` toutes les 5 secondes ;
- `onUnmounted()` arrête le timer quand le composant disparaît.

Phrase utile :

> Je n'ai pas utilisé WebSocket. J'ai choisi un polling simple parce que c'est suffisant pour ce projet et plus facile à expliquer et maintenir.

## 10. Questions probables du prof

### Pourquoi avoir utilisé des composables ?

Réponse :

> Pour séparer la logique de l'affichage. Par exemple, `usePolls()` contient les appels API du CRUD, donc `AppPollDashboard.vue` ne contient pas tous les détails des requêtes.

### Pourquoi ne pas tout mettre dans un seul composant ?

Réponse :

> Parce que le composant serait trop long et difficile à maintenir. J'ai séparé par responsabilité : édition, paramètres, vote, résultats.

### Est-ce qu'une `computed` aurait pu être utilisée ici ?

Réponse :

> Oui, si la valeur dépend d'autres valeurs réactives et ne doit pas être modifiée directement. Par exemple `isEditing` est une bonne `computed` parce qu'elle dépend de `props.poll`.

### Quelle est la différence entre `ref` et `computed` ?

Réponse :

> `ref` stocke une valeur réactive que je peux modifier. `computed` calcule une valeur à partir d'autres valeurs réactives.

### Pourquoi valider côté backend si le frontend valide déjà ?

Réponse :

> Le frontend peut être contourné. Le backend doit toujours valider les données et les droits, car c'est lui qui protège réellement l'application.

### Comment les résultats sont-ils mis à jour en direct ?

Réponse :

> Le composant résultats lance un polling toutes les 5 secondes. Il rappelle l'endpoint `/results`, puis Vue met automatiquement l'affichage à jour grâce à la réactivité.

### Comment empêches-tu le vote après expiration ?

Réponse :

> Le modèle `Poll` a une méthode `isExpired()`. L'API vérifie cette méthode avant d'accepter un vote. Le frontend affiche aussi un message clair pour l'utilisateur.

### Comment fonctionnent les résultats privés ?

Réponse :

> Dans `results()`, l'API vérifie si `results_public` est vrai ou si l'utilisateur connecté est propriétaire. Sinon elle retourne une erreur 403.

### Comment le lien de partage fonctionne ?

Réponse :

> Chaque sondage a un `secret_token`. Le lien contient ce token. La page `/polls/vote/{token}` charge Vue, puis Vue appelle l'API `/api/v1/polls/token/{token}` pour récupérer le sondage.

## 11. Ce qu'il faut apprendre avant l'oral

Priorité 1 :

- expliquer le parcours complet : création -> partage -> vote -> résultats ;
- connaître les composants Vue principaux ;
- connaître les endpoints API ;
- expliquer la différence entre frontend et backend ;
- expliquer `ref`, `computed`, `props`, `emit`, `v-model`.

Priorité 2 :

- expliquer les relations Eloquent :
  - un `User` a plusieurs `Poll` ;
  - un `Poll` a plusieurs `PollOption` ;
  - un `Poll` a plusieurs `PollVote` ;
  - un `PollVote` appartient à une option et à un utilisateur.

Priorité 3 :

- expliquer le polling ;
- expliquer la validation Laravel avec les `FormRequest` ;
- expliquer pourquoi les droits sont vérifiés côté API.

## 12. Mini script à dire pendant l'oral

Tu peux t'entraîner avec ce texte :

> Mon projet est une application de sondage en Laravel et Vue 3. Laravel expose une API JSON versionnée dans `/api/v1`, et Vue consomme cette API pour afficher le dashboard, créer les sondages, voter et afficher les résultats.
>
> Le dashboard est accessible à un utilisateur connecté. Il peut créer un sondage avec une question, plusieurs options, une couleur et des paramètres comme le choix multiple, les résultats publics, la durée et le statut brouillon ou lancé.
>
> Quand le sondage est lancé, un lien de partage est disponible. Ce lien contient un token secret. La page de vote utilise ce token pour récupérer le sondage via l'API. Le vote demande une authentification, mais les résultats peuvent être publics selon la configuration.
>
> Côté Vue, j'ai séparé l'interface en composants : un composant pour l'éditeur, un pour les paramètres, un pour le vote et un pour les résultats. J'ai aussi utilisé des composables pour regrouper la logique API et éviter de surcharger les composants.
>
> Côté Laravel, les routes API sont dans `routes/api.php`. Le contrôleur principal est `ApiPollController`. Les données sont validées avec des `FormRequest`, et les droits sont vérifiés côté backend pour empêcher un utilisateur de modifier un sondage qui ne lui appartient pas.
>
> Pour les résultats en direct, j'utilise un polling toutes les 5 secondes. C'est simple et suffisant pour ce projet. Les résultats sont affichés avec des barres de progression CSS.

## 13. Adaptation live possible

Le prof peut demander une petite modification en direct.

Exemples probables :

- changer l'intervalle du polling de 5 secondes à 3 secondes ;
- ajouter un texte dans le message de sondage expiré ;
- ajouter une couleur de thème ;
- modifier une validation ;
- ajouter un champ affiché dans le dashboard ;
- changer le label d'un bouton ;
- afficher le nombre d'options dans une carte de sondage.

Où modifier :

```text
Changer le polling :
  resources/js/components/PollResults.vue
  Ligne logique : usePolling(() => fetchResults(props.token), 5000)

Changer le message de sondage expiré :
  resources/js/components/PollVote.vue

Ajouter une couleur :
  resources/js/utils/pollColors.js
  app/Http/Requests/StorePollRequest.php
  app/Http/Requests/UpdatePollRequest.php

Changer une règle de validation :
  app/Http/Requests/StorePollRequest.php
  app/Http/Requests/UpdatePollRequest.php
  app/Http/Requests/StorePollVoteRequest.php

Changer les routes API :
  routes/api.php

Changer la logique principale :
  app/Http/Controllers/Api/v1/ApiPollController.php
```

## 14. Points faibles à assumer correctement

Si le prof demande pourquoi certains choix sont simples :

- Le polling est plus simple que WebSocket, mais adapté au projet.
- Il n'y a pas de gros store global type Pinia, car l'application reste assez petite.
- Les composables suffisent pour partager la logique.
- La sécurité importante est côté Laravel.
- Le frontend est mobile first avec Tailwind et des composants séparés.

Phrase utile :

> J'ai préféré garder une architecture simple et défendable plutôt que d'ajouter des outils plus complexes qui n'étaient pas nécessaires pour ce projet.

## 15. Checklist juste avant l'oral

- Savoir lancer le projet.
- Avoir un utilisateur connecté prêt.
- Avoir au moins un sondage existant.
- Avoir un sondage avec résultats publics.
- Avoir un sondage avec résultats privés.
- Avoir un sondage en brouillon.
- Avoir un sondage lancé.
- Tester le lien `/polls/vote/{token}`.
- Savoir ouvrir rapidement :
  - `routes/api.php`
  - `ApiPollController.php`
  - `AppPollDashboard.vue`
  - `PollEditor.vue`
  - `PollVote.vue`
  - `PollResults.vue`
  - `usePolls.js`
  - `usePollVoting.js`
  - `usePolling.js`
