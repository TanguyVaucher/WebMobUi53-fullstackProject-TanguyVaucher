<script setup>
import { ref, onMounted } from 'vue';
import PollGrid    from './components/PollGrid.vue';
import PollEditor  from './components/PollEditor.vue';
import PollVote    from './components/PollVote.vue';
import PollResults from './components/PollResults.vue';
import { usePolls } from './composables/usePolls';

const props = defineProps({
    token:    { type: String, default: null },
    loginUrl: { type: String, default: null },
    username: { type: String, default: null },
});

const { polls, loading, error, fetchPolls, deletePoll, updatePoll } = usePolls();

// Question du sondage affiché en résultats (reçue depuis PollResults une fois chargé)
const resultsPoll = ref(null);

// Image dans public/ — référencée comme string pour éviter que Vite la bundle

// Vue active — "list" | "create" | "edit" | "vote" | "results"
const view         = ref(props.token ? 'vote' : 'list');
const selectedPoll = ref(null);
const activeToken  = ref(props.token ?? null);
const liveQuestion = ref('');

onMounted(() => {
    if (!props.token) fetchPolls();
});

// Titre affiché dans le header selon la vue
const viewTitle = {
    list:    'Mes sondages',
    create:  'Nouveau sondage',
    edit:    'Modifier',
    vote:    'Voter',
    results: 'Résultats',
};

function openCreate() {
    selectedPoll.value = null;
    view.value = 'create';
}

function openEdit(poll) {
    selectedPoll.value = poll;
    liveQuestion.value = poll.question;
    view.value = 'edit';
}

function openResults(poll) {
    selectedPoll.value = poll ?? null;
    resultsPoll.value  = poll ?? null;
    activeToken.value  = poll?.secret_token ?? activeToken.value;
    view.value = 'results';
}

function backToList() {
    selectedPoll.value = null;
    view.value = 'list';
}

function onSaved() {
    fetchPolls();
    backToList();
}

async function onDelete(poll) {
    if (!confirm(`Supprimer ce sondage ?`)) return;
    await deletePoll(poll.id);
}

async function onPublish(poll) {
    await updatePoll(poll.id, { is_draft: false });
    fetchPolls();
}

function onVoted() {
    view.value = 'results';
}
</script>

<template>
    <!--
        Layout SPA full-screen :
        - header fixe en haut
        - contenu scrollable en dessous (flex-1 overflow-y-auto)
        Le tout tient dans h-screen sans déborder.
    -->
    <div class="h-screen flex flex-col bg-gradient-to-br from-slate-50 via-white to-indigo-50/30 overflow-hidden">

        <!-- ─── Header fixe ─────────────────────────────────────────── -->
        <header class="shrink-0 flex items-center justify-between px-6 py-4
                        border-b border-slate-100 bg-white/70 backdrop-blur-md">

            <!-- Gauche : logo + titre de la vue -->
            <div class="flex items-center gap-3">
                <!-- Bouton retour si pas sur la liste -->
                <button
                    v-if="view !== 'list' && !props.token"
                    @click="backToList"
                    style="margin-top: -4px"
                    class="w-9 h-9 flex items-center justify-center rounded-xl
                           bg-black/10 hover:bg-black/20 text-slate-700
                           transition-colors duration-150 text-lg"
                >
                    ←
                </button>

                <div>
                    <!-- Titre dynamique selon la vue -->
                    <h1 class="text-3xl font-semibold tracking-tight leading-tight flex items-center gap-2" style="margin-top: -4px">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-indigo-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="8"  y1="18" x2="8"  y2="14"/>
                            <line x1="12" y1="18" x2="12" y2="11"/>
                            <line x1="16" y1="18" x2="16" y2="13"/>
                        </svg>

                        <!-- Dashboard et création -->
                        <template v-if="view === 'list' || view === 'create'">
                            <span class="text-slate-900/80">Les sondages de </span><span class="text-indigo-500">{{ props.username }}</span>
                        </template>

                        <!-- Édition : titre du sondage -->
                        <template v-else-if="view === 'edit'">
                            <span style="color: rgba(0,0,0,0.8)">Modifier :</span><span class="text-indigo-500 truncate max-w-xl"> {{ liveQuestion }}</span>
                        </template>

                        <!-- Résultats : "Résultats pour <question>" -->
                        <template v-else-if="view === 'results'">
                            <span class="text-slate-900/80">Résultats pour </span>
                            <span class="text-indigo-500 truncate max-w-xl">{{ resultsPoll?.question ?? '...' }}</span>
                        </template>

                        <!-- Autres vues -->
                        <template v-else>
                            <span class="text-slate-900/80">{{ viewTitle[view] }}</span>
                        </template>
                    </h1>
                </div>
            </div>

            <!-- Droite : bouton Nouveau (liste) ou label couleur (create/edit) -->
            <div id="header-right-portal"></div>
            <button
                v-if="view === 'list'"
                @click="openCreate"
                class="rounded-xl bg-indigo-500 hover:bg-indigo-600 text-white
                       font-bold px-4 py-2 text-sm transition-all duration-150
                       shadow-sm shadow-indigo-200"
            >
                + Nouveau sondage
            </button>

        </header>

        <!-- ─── Contenu scrollable ───────────────────────────────────── -->
        <main class="flex-1 overflow-y-auto">

            <!-- Erreur -->
            <div v-if="error && view === 'list'"
                 class="mx-6 mt-4 rounded-2xl bg-red-50 border border-red-100
                        px-4 py-3 text-sm text-red-500">
                {{ error }}
            </div>

            <!-- Spinner -->
            <div v-if="loading && view === 'list'"
                 class="flex flex-col items-center justify-center h-64 text-slate-400">
                <div class="w-8 h-8 border-2 border-indigo-200 border-t-indigo-500
                            rounded-full animate-spin mb-3"></div>
                Chargement...
            </div>

            <!-- Grille des sondages -->
            <PollGrid
                v-else-if="view === 'list'"
                :polls="polls"
                @create="openCreate"
                @edit="openEdit"
                @delete="onDelete"
                @publish="onPublish"
                @results="openResults"
            />

            <!-- Éditeur dans un conteneur centré + padding -->
            <div v-else-if="view === 'create' || view === 'edit'"
                 class="min-h-full flex items-center" style="margin-top: -40px">
            <div class="max-w-6xl w-full mx-auto px-8 py-8">
                <PollEditor
                    :poll="selectedPoll"
                    @saved="onSaved"
                    @cancel="backToList"
                    @question-change="q => liveQuestion = q"
                />
            </div>
            </div>

            <!-- Vote -->
            <div v-else-if="view === 'vote'"
                 class="max-w-lg mx-auto px-6 py-6">
                <PollVote
                    :token="activeToken"
                    @voted="onVoted"
                    @results="openResults(null)"
                />
            </div>

            <!-- Résultats -->
            <div v-else-if="view === 'results'"
                 class="max-w-lg mx-auto px-6 py-6">
                <PollResults
                    :token="activeToken"
                    :has-back="!props.token"
                    @back="backToList"
                    @poll-loaded="p => resultsPoll = p"
                />
            </div>
        </main>
    </div>
</template>
