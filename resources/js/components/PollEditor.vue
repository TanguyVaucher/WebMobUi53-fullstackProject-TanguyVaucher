<!-- Composant qui centralise l’édition d’un sondage, puis l'enregistre via l’API -->

<script setup>
import { ref, computed } from 'vue';
import PollOptionsEditor from './PollOptionsEditor.vue';
import PollSettings from './PollSettings.vue';
import PollShareLink from './PollShareLink.vue';
import { usePolls } from '../composables/usePolls';
import { POLL_COLORS } from '../utils/pollColors';

// Reçoit depuis AppPollDashboard.vue le sondage à éditer (null en mode création)
const props = defineProps({
    poll: { type: Object, default: null },
});

// Émet au parent la sauvegarde du sondage et les changements de question (Header réactif)
const emit = defineEmits(['saved', 'question-change']);

// Récupèrer depuis le composable usePolls create et edit
const { createPoll, updatePoll } = usePolls();
// Détecter un edit quand un sondage est reçu en props
const isEditing = computed(() => props.poll !== null);

// Déterminer une couleur de thème au hasard (jamais la même que la précédente)
const colorKeys = Object.keys(POLL_COLORS);
let _lastPicked = null;
function randomColor(current = null) {
    const exclude = current ?? _lastPicked; // Couleur à explure
    const choices = colorKeys.filter(k => k !== exclude); // Tableau sans l'exclusion
    const picked = choices[Math.floor(Math.random() * choices.length)]; // Choix de la couleur
    _lastPicked = picked;
    return picked;
}

// Initialisation de l’état local du formulaire à partir du sondage existant, ou avec des valeurs par défaut en mode création
const question = ref(props.poll?.question ?? '');
const color = ref(props.poll?.color ?? randomColor(null));  // Couleur aléatoire par défaut
const options = ref(
    props.poll?.options?.map(o => ({ id: o.id, label: o.label }))
    ?? [{ label: '' }, { label: '' }]
);
const settings = ref({
    allow_multiple_choices: props.poll?.allow_multiple_choices ?? false,
    allow_vote_change: props.poll?.allow_vote_change ?? false,
    results_public: props.poll?.results_public ?? false,
    duration: props.poll?.duration ?? 3600,
    is_draft: props.poll?.is_draft ?? true,
});

// Variables d'état
const loading = ref(false);
const error = ref(null);
const questionFocused = ref(false);
const questionHovered = ref(false);

// Style du champ question
const questionStyle = computed(() => {
    const solid = POLL_COLORS[color.value]?.solid ?? '#6366f1';
    let bgOpacity = '0D';                          // 5% repos
    if (questionHovered.value) bgOpacity = '1A';   // ~10% hover
    if (questionFocused.value) bgOpacity = '26';   // ~15% focus
    return {
        border: `2px solid ${solid}`,
        background: `${solid}${bgOpacity}`,
        transition: 'background 0.2s ease, border-color 0.2s ease',
        outline: 'none',
    };
});
const questionIconColor = computed(() =>
    POLL_COLORS[color.value]?.solid ?? '#6366f1'
);

// Dépublier un sondage (brouillon)
async function onDepublish() {
    // Si pas encore en base
    if (!props.poll?.id) {
        settings.value = { ...settings.value, is_draft: true };
        return;
    }

    await updatePoll(props.poll.id, { is_draft: true });
    settings.value = { ...settings.value, is_draft: true };
}

// Sauvegarder le sondage
async function submit() {
    error.value = null;
    if (!question.value.trim()) { error.value = 'La question est requise.'; return; }
    if (options.value.some(o => !o.label.trim())) { error.value = 'Toutes les options doivent avoir un label.'; return; }
    if (!settings.value.duration) { error.value = 'Une durée est requise.'; return; }

    // Indique qu’une sauvegarde est en cours, puis prépare les données du sondage à envoyer à l’API (createPoll() ou updatePoll())
    loading.value = true;
    const payload = {
        question: question.value.trim(),
        color: color.value,
        options: options.value.map(o => ({ id: o.id, label: o.label.trim() })),
        ...settings.value,
    };

    try {
        const result = isEditing.value
            ? await updatePoll(props.poll.id, payload)
            : await createPoll(payload);
        emit('saved', result);
    } catch (err) {
        if (err?.data?.errors) {
            error.value = Object.values(err.data.errors).flat().join(' ');
        } else {
            error.value = err?.data?.message || 'Une erreur est survenue.';
        }
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="space-y-6">

        <!-- Messages d'erreur -->
        <div v-if="error" class="rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-500">
            {{ error }}
        </div>

        <!-- Formulaire création / édition -->
        <form @submit.prevent="submit">
            <div class="bg-white border border-slate-100 overflow-hidden"
                style="border-radius: 28px; box-shadow: 0 8px 40px 0 rgba(0,0,0,0.06)">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 items-stretch">

                    <!-- Colonne de gauche -->
                    <div
                        class="space-y-6 px-5 sm:px-8 py-6 sm:py-8 border-b lg:border-b-0 lg:border-r border-slate-200">

                        <!-- Question du sondage -->
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="absolute left-5 top-1/2 -translate-y-1/2 pointer-events-none shrink-0"
                                :style="{ color: questionIconColor }">
                                <path
                                    d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" />
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                                <line x1="12" x2="12.01" y1="17" y2="17" />
                            </svg>
                            <input v-model="question" type="text" placeholder="Quelle est votre question ?"
                                @input="emit('question-change', question)" class="w-full rounded-2xl pl-[60px] sm:pl-[72px] pr-4 sm:pr-6 py-4 sm:py-5 text-2xl sm:text-3xl font-semibold
                                   tracking-tight placeholder:opacity-80 outline-none"
                                :style="{ ...questionStyle, color: questionIconColor }" @focus="questionFocused = true"
                                @blur="questionFocused = false" @mouseenter="questionHovered = true"
                                @mouseleave="questionHovered = false" />
                        </div>

                        <!-- Choix du thème de couleur -->
                        <div class="space-y-2">
                            <label
                                class="text-sm font-semibold uppercase tracking-normal text-slate-400 mb-3 block">Couleur
                                du thème</label>
                            <div class="flex items-center gap-2 flex-wrap" style="min-height: 44px">
                                <!-- Bouton random -->
                                <button type="button" @click="color = randomColor(color.value)" class="w-8 h-8 rounded-full border-2 flex items-center justify-center
                                       text-slate-500 bg-slate-100 hover:bg-slate-200 border-transparent
                                       transition-all duration-150" title="Couleur aléatoire">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="16 3 21 3 21 8" />
                                        <line x1="4" y1="20" x2="21" y2="3" />
                                        <polyline points="21 16 21 21 16 21" />
                                        <line x1="4" y1="4" x2="21" y2="21" />
                                    </svg>
                                </button>
                                <!-- Boutons couleurs -->
                                <button v-for="(def, key) in POLL_COLORS" :key="key" type="button" @click="color = key"
                                    class="rounded-full transition-all duration-200"
                                    :style="{ background: def.solid, width: color === key ? '36px' : '30px', height: color === key ? '36px' : '30px', border: color === key ? `4px solid ${def.solid}` : '2px solid transparent' }"
                                    :title="def.label"></button>
                            </div>
                        </div>

                        <!-- Options (délégué à PollOptionsEditor) -->
                        <div class="space-y-1.5">
                            <label
                                class="text-sm font-semibold uppercase tracking-normal text-slate-400 mb-3 block">Choix
                                des options</label>
                            <PollOptionsEditor v-model="options" :accent-color="questionIconColor" />
                        </div>
                    </div>

                    <!-- Colonne de droite -->
                    <div class="space-y-6 px-5 sm:px-8 py-6 sm:py-8">

                        <!-- Paramètres (délégué à PollSettings) -->
                        <div class="rounded-2xl border border-slate-100 bg-slate-100/60 px-4 py-4">
                            <p class="text-sm font-semibold uppercase tracking-wider text-slate-400 mb-4">Paramètres du
                                sondage</p>
                            <PollSettings v-model="settings" @depublish="onDepublish" />
                        </div>

                        <!-- Lien de partage si édition d'un sondage lancé (délégué à PollShareLink) -->
                        <div v-if="isEditing && poll?.secret_token" class="space-y-1.5">
                            <label
                                class="text-sm font-semibold uppercase tracking-normal text-slate-400 mb-1.5 block">Lien
                                de partage</label>
                            <PollShareLink :token="poll.secret_token" />
                        </div>

                        <!-- Bouton submit -->
                        <button type="submit" :disabled="loading" class="w-full rounded-2xl bg-indigo-500 hover:bg-indigo-600 disabled:opacity-50
                               text-white font-bold py-3.5 text-xl tracking-wide transition-all duration-150">
                            {{ loading ? 'Sauvegarde...' : (isEditing ? 'Enregistrer' : 'Créer') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
