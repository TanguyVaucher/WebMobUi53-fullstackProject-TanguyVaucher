<script setup>
import { ref, onMounted } from 'vue';
import PollGrid    from './components/PollGrid.vue';
import PollEditor  from './components/PollEditor.vue';
import PollVote    from './components/PollVote.vue';
import PollResults from './components/PollResults.vue';
import { usePolls } from './composables/usePolls';

// Déclaration des props (données passées à l'app)
const props = defineProps({
    token:    { type: String, default: null },
    loginUrl: { type: String, default: null },
    username: { type: String, default: null },
});

// Recupère depuis le composable usePolls l'etat reactif des sondages et leurs fonctions
const { polls, loading, error, fetchPolls, deletePoll, updatePoll } = usePolls();

// App.vue principale

const resultsPoll = ref(null);// Question du sondage affiché en résultats (reçue depuis PollResults une fois chargé)
const view         = ref(props.token ? 'vote' : 'list'); // Vue active (si un token est fournis -> "vote", sinon "list" = le dashboard)
const selectedPoll = ref(null); // Sondage séléctionné
const activeToken  = ref(props.token ?? null); //Token actuellement utilisé
const liveQuestion = ref(''); // Question du sondage en cours d'édition

// Charger les sondages dans le dashboard si aucun token n'a été passé
onMounted(() => {
    if (!props.token) fetchPolls();
});

// Charger la vue de création, désélectionner le sondage
function openCreate() {
    selectedPoll.value = null;
    view.value = 'create';
}

// Charger la vue d'édition d'un sondage
function openEdit(poll) {
    selectedPoll.value = poll;
    liveQuestion.value = poll.question; // Header dynamique
    view.value = 'edit';
}

// Charger la vue des résultats d'un sondage
function openResults(poll) {
    selectedPoll.value = poll ?? null;
    resultsPoll.value  = poll ?? null;
    activeToken.value  = poll?.secret_token ?? activeToken.value;
    view.value = 'results';
}

// Retour au dashboard
function backToList() {
    selectedPoll.value = null;
    view.value = 'list';
}

// Revenir depuis les resultats vers le vote (si un token est actif)
function backFromResults() {
    if (props.token) {
        view.value = 'vote';
    } else {
        backToList();
    }
}

// Sauvegarde -> recharger les sondages puis revenir au dashboard
function onSaved() {
    fetchPolls();
    backToList();
}

// Supprimer un sondage, avec confirmation
async function onDelete(poll) {
    if (!confirm(`Supprimer ce sondage ?`)) return;
    await deletePoll(poll.id); // Attendre la suppression effective avant de continuer
}

// Publier un sondage en brouillon
async function onPublish(poll) {
    await updatePoll(poll.id, { is_draft: false }); // Attendre l'update effective
    fetchPolls(); // Recherger la liste des sondages
}

// Charger la vue des résultat après un vote
function onVoted() {
    view.value = 'results';
}
</script>

<template>
    <!--
        Layout SPA du module de polls
    -->
    <div class="h-screen flex flex-col bg-gradient-to-br from-slate-50 via-white to-indigo-50/30 overflow-hidden">

        <!-- Header -->
        <header class="shrink-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 sm:px-6 py-4
                        border-b border-slate-100 bg-white/70 backdrop-blur-md">

            <!-- Gauche : logo et titre -->
            <div class="flex items-center gap-3 w-full sm:w-auto min-w-0">
                <!-- Bouton retour contextuel -->
                <button
                    v-if="view !== 'list' && (!props.token || view === 'results')"
                    @click="view === 'results' ? backFromResults() : backToList()"
                    style="margin-top: -4px"
                    class="w-9 h-9 flex items-center justify-center rounded-xl
                           bg-black/10 hover:bg-black/20 text-slate-700
                           transition-colors duration-150 text-lg"
                >
                    ←
                </button>

                <div>
                    <!-- Titre dynamique selon la vue -->
                    <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight leading-tight flex items-center gap-2 min-w-0" style="margin-top: -4px">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-indigo-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="8"  y1="18" x2="8"  y2="14"/>
                            <line x1="12" y1="18" x2="12" y2="11"/>
                            <line x1="16" y1="18" x2="16" y2="13"/>
                        </svg>

                        <!-- Titre dashboard et création -->
                        <template v-if="view === 'list' || view === 'create'">
                            <span class="text-slate-900/80">Les sondages de </span><span class="text-indigo-500">{{ props.username }}</span>
                        </template>

                        <!-- Titre édition (titre réactif) -->
                        <template v-else-if="view === 'edit'">
                            <span style="color: rgba(0,0,0,0.8)">Modifier :</span><span class="text-indigo-500 truncate max-w-[14rem] sm:max-w-xl"> {{ liveQuestion }}</span>
                        </template>

                        <!-- Titre résultats pour <question> -->
                        <template v-else-if="view === 'results'">
                            <span class="text-slate-900/80">Résultats pour </span>
                            <span class="text-indigo-500 truncate max-w-[14rem] sm:max-w-xl">{{ resultsPoll?.question ?? '...' }}</span>
                        </template>

                        <!-- Titre voter -->
                        <template v-else>
                            <span class="text-slate-900/80">Voter</span>
                        </template>
                    </h1>
                </div>
            </div>

            <!-- Droite : bouton de création d'un nouveau sondage -->
            <div id="header-right-portal"></div>
            <button
                v-if="view === 'list'"
                @click="openCreate"
                class="w-full sm:w-auto rounded-xl bg-indigo-500 hover:bg-indigo-600 text-white
                       font-bold px-4 py-2 text-sm transition-all duration-150
                       shadow-sm shadow-indigo-200"
            >
                + Nouveau sondage
            </button>
        </header>

        <!-- Layout principal scrollable -->
        <main class="flex-1 overflow-y-auto">

            <!-- Message d'erreur -->
            <div v-if="error && view === 'list'"
                 class="mx-6 mt-4 rounded-2xl bg-red-50 border border-red-100
                        px-4 py-3 text-sm text-red-500">
                {{ error }}
            </div>

            <!-- Placeholder de chargement -->
            <div v-if="loading && view === 'list'"
                 class="flex flex-col items-center justify-center h-64 text-slate-400">
                <div class="w-8 h-8 border-2 border-indigo-200 border-t-indigo-500
                            rounded-full animate-spin mb-3"></div>
                Chargement...
            </div>

            <!-- Grille des sondages pour le dashboard -->
            <!-- :polls="polls" -> passer la liste des sondages -->
            <!-- @ -> écoute les événements emis par PollGrid.vue pour lancer les bonnes fonctions -->
            <PollGrid
                v-else-if="view === 'list'"
                :polls="polls"
                @create="openCreate"
                @edit="openEdit"
                @delete="onDelete"
                @publish="onPublish"
                @results="openResults"
            />

            <!-- Dans create et edit, afficher l'éditeur de poll -->
            <div v-else-if="view === 'create' || view === 'edit'"
                 class="min-h-full flex items-center lg:-mt-10">
            <!-- :poll="selectedPoll" -> passer le sondage à éditer, ou null -->
            <!-- @ -> écoute les événements emis par PollEditor.vue pour lancer les bonnes fonctions -->
            <div class="max-w-6xl w-full mx-auto px-4 sm:px-8 py-6 sm:py-8">
                <PollEditor
                    :poll="selectedPoll"
                    @saved="onSaved"
                    @cancel="backToList"
                    @question-change="q => liveQuestion = q"
                />
            </div>
            </div>

            <!-- Vue de vote du sondage -->
            <div v-else-if="view === 'vote'"
                 class="max-w-lg mx-auto px-6 py-6">
                <!-- :token="activeToken" -> passe le token courant au composant -->
                <!-- @ -> écoute les événements emis par PollVote.vue pour ouvrir les resultats -->
                <PollVote
                    :token="activeToken"
                    @voted="onVoted"
                    @results="openResults(null)"
                />
            </div>

            <!-- Vue des resultats -->
            <div v-else-if="view === 'results'"
                 class="max-w-lg mx-auto px-6 py-6">
                <!-- : -> passe au composant le token et les libellés du bouton retour -->
                <!-- @ -> écoute les évenéments emis par PollResults.vue -->
                <PollResults
                    :token="activeToken"
                    :has-back="true"
                    :back-label="props.token ? '← Retour au sondage' : '← Retour au dashboard'"
                    @back="backFromResults"
                    @poll-loaded="p => resultsPoll = p"
                />
            </div>

        </main>
    </div>
</template>
