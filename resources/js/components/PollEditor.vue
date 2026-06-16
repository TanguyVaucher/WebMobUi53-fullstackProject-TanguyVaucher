<script setup>
import { ref, computed } from 'vue';
import PollOptionsEditor from './PollOptionsEditor.vue';
import PollSettings from './PollSettings.vue';
import PollShareLink from './PollShareLink.vue';
import { usePolls } from '../composables/usePolls';
import { POLL_COLORS } from '../utils/pollColors';

const props = defineProps({
    poll: { type: Object, default: null },
});
const emit = defineEmits(['saved', 'cancel', 'question-change']);

const { createPoll, updatePoll } = usePolls();
const isEditing = computed(() => props.poll !== null);

// Choisit une couleur au hasard, jamais la même que la précédente
const colorKeys = Object.keys(POLL_COLORS);
let _lastPicked = null;
function randomColor(current = null) {
    const exclude = current ?? _lastPicked;
    const choices = colorKeys.filter(k => k !== exclude);
    const picked = choices[Math.floor(Math.random() * choices.length)];
    _lastPicked = picked;
    return picked;
}

// État local du formulaire
const question = ref(props.poll?.question ?? '');
const color = ref(props.poll?.color ?? randomColor(null));  // thème aléatoire par défaut
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

const loading = ref(false);
const error = ref(null);
const questionFocused = ref(false);
const questionHovered = ref(false);

// Style dynamique du champ question selon la couleur choisie
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

// Prévisualisation — même effet que les cards du dashboard
const previewStyle = computed(() => {
    if (!color.value) return { background: '#f1f5f9' };
    const c = POLL_COLORS[color.value];
    return {
        background: `linear-gradient(150deg, ${c.from}, ${c.to}, ${c.via}, ${c.from})`,
        backgroundSize: '400% 400%',
        animation: 'gradientShift 5s ease infinite',
    };
});

async function onDepublish() {
    if (!props.poll?.id) {
        settings.value = { ...settings.value, is_draft: true };
        return;
    }

    await updatePoll(props.poll.id, { is_draft: true });
    settings.value = { ...settings.value, is_draft: true };
}

async function submit() {
    error.value = null;
    if (!question.value.trim()) { error.value = 'La question est requise.'; return; }
    if (options.value.some(o => !o.label.trim())) { error.value = 'Toutes les options doivent avoir un label.'; return; }
    if (!settings.value.duration) { error.value = 'Une durée est requise.'; return; }

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

        <!-- Message d'erreur -->
        <div v-if="error" class="rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-500">
            {{ error }}
        </div>

        <form @submit.prevent="submit">
            <div class="bg-white border border-slate-100 overflow-hidden"
                style="border-radius: 28px; box-shadow: 0 8px 40px 0 rgba(0,0,0,0.06)">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 items-stretch">

                    <!-- ── Colonne gauche : question + couleur + options ── -->
                    <div
                        class="space-y-6 px-5 sm:px-8 py-6 sm:py-8 border-b lg:border-b-0 lg:border-r border-slate-200">

                        <!-- Question — champ géant -->
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

                        <!-- Thème couleur -->
                        <div class="space-y-2">
                            <label
                                class="text-sm font-semibold uppercase tracking-normal text-slate-400 mb-3 block">Couleur
                                du thème</label>
                            <div class="flex items-center gap-2 flex-wrap" style="min-height: 44px">
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
                                <button v-for="(def, key) in POLL_COLORS" :key="key" type="button" @click="color = key"
                                    class="rounded-full transition-all duration-200"
                                    :style="{ background: def.solid, width: color === key ? '36px' : '30px', height: color === key ? '36px' : '30px', border: color === key ? `4px solid ${def.solid}` : '2px solid transparent' }"
                                    :title="def.label"></button>
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="space-y-1.5">
                            <label
                                class="text-sm font-semibold uppercase tracking-normal text-slate-400 mb-3 block">Choix
                                des options</label>
                            <PollOptionsEditor v-model="options" :accent-color="questionIconColor" />
                        </div>
                    </div>

                    <!-- ── Colonne droite : paramètres + partage + soumettre ── -->
                    <div class="space-y-6 px-5 sm:px-8 py-6 sm:py-8">

                        <!-- Paramètres -->
                        <div class="rounded-2xl border border-slate-100 bg-slate-100/60 px-4 py-4">
                            <p class="text-sm font-semibold uppercase tracking-wider text-slate-400 mb-4">Paramètres du
                                sondage</p>
                            <PollSettings v-model="settings" @depublish="onDepublish" />
                        </div>

                        <!-- Lien de partage si édition d'un sondage lancé -->
                        <div v-if="isEditing && poll?.secret_token" class="space-y-1.5">
                            <label
                                class="text-sm font-semibold uppercase tracking-normal text-slate-400 mb-1.5 block">Lien
                                de partage</label>
                            <PollShareLink :token="poll.secret_token" />
                        </div>

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

<!-- Animation du dégradé — pas de @apply donc CSS pur, pas de scoped pour éviter les conflits Tailwind v4 -->
<style>
@keyframes gradientShift {
    0% {
        background-position: 0% 0%;
    }

    25% {
        background-position: 100% 50%;
    }

    50% {
        background-position: 50% 100%;
    }

    75% {
        background-position: 0% 50%;
    }

    100% {
        background-position: 0% 0%;
    }
}
</style>
