<script setup>
import { onMounted, computed } from 'vue';
import { usePollVoting } from '../composables/usePollVoting';
import { POLL_COLORS } from '../utils/pollColors';

const props = defineProps({
    token: { type: String, required: true },
});
const emit = defineEmits(['voted', 'results']);

const {
    poll, isOwner, hasVoted, expired,
    selectedOptions, loading, error, voteError,
    fetchPollByToken, submitVote, toggleOption,
} = usePollVoting();

// Charge le sondage au montage du composant
onMounted(() => fetchPollByToken(props.token));

async function handleVote() {
    if (selectedOptions.value.length === 0) return;
    const ok = await submitVote(props.token);
    if (ok) emit('voted');
}

// Vérifie si une option est sélectionnée
function isSelected(id) {
    return selectedOptions.value.includes(id);
}

const themeC = computed(() => poll.value?.color ? POLL_COLORS[poll.value.color] : null);

const questionGradientStyle = computed(() => {
    if (!themeC.value) return {};
    const c = themeC.value;
    return {
        background: `linear-gradient(135deg, ${c.from}, ${c.to}, ${c.via}, ${c.from})`,
        backgroundSize: '400% 400%',
        animation: 'gradientShift 5s ease infinite',
        WebkitBackgroundClip: 'text',
        WebkitTextFillColor: 'transparent',
        backgroundClip: 'text',
    };
});

const selectedOptionStyle = computed(() => {
    if (!themeC.value) return {};
    const solid = themeC.value.solid;
    return {
        borderColor: solid,
        backgroundColor: solid + '18',
        color: solid,
        boxShadow: `0 1px 4px ${solid}33`,
    };
});

const radioSelectedStyle = computed(() => {
    if (!themeC.value) return {};
    return { borderColor: themeC.value.solid, backgroundColor: themeC.value.solid };
});

const buttonGradientStyle = computed(() => {
    if (!themeC.value) return {};
    const c = themeC.value;
    return {
        background: `linear-gradient(135deg, ${c.from}, ${c.to}, ${c.via}, ${c.from})`,
        backgroundSize: '400% 400%',
        animation: 'gradientShift 5s ease infinite',
    };
});

const themeTextStyle = computed(() => themeC.value ? { color: themeC.value.solid } : {});
</script>

<template>
    <div class="space-y-6">

        <!-- Chargement -->
        <div v-if="loading" class="text-center py-16 text-slate-400">
            <div class="w-8 h-8 border-2 border-indigo-300 border-t-indigo-500 rounded-full animate-spin mx-auto mb-3"></div>
            Chargement...
        </div>

        <!-- Erreur (404, brouillon…) -->
        <div v-else-if="error" class="rounded-2xl bg-red-50 border border-red-100 px-6 py-8 text-center">
            <p class="text-2xl mb-2">⚠️</p>
            <p class="font-bold text-red-600">{{ error }}</p>
        </div>

        <template v-else-if="poll">
            <!-- En-tête — titre pleine largeur viewport (breakout du conteneur max-w-lg) -->
            <div style="position: relative; left: 50%; transform: translateX(-50%); width: calc(100vw - 20px); padding-bottom: 15px;">
                <h2 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight text-center"
                    :style="[
                        { maxWidth: '700px', margin: '0 auto', lineHeight: '1.08', wordBreak: 'break-word' },
                        questionGradientStyle.background ? questionGradientStyle : { color: '#0f172a' }
                    ]">
                    {{ poll.question }}
                </h2>
            </div>

            <!-- Sondage expiré -->
            <div v-if="expired" class="rounded-2xl bg-slate-100 border border-slate-200 px-5 py-4 flex items-center gap-3">
                <span class="text-xl">⏱</span>
                <div>
                    <p class="font-bold text-slate-600">Sondage terminé</p>
                    <p class="text-sm text-slate-400">Ce sondage n'accepte plus de nouveaux votes.</p>
                </div>
            </div>

            <!-- Déjà voté -->
            <div v-else-if="hasVoted && !poll.allow_vote_change" class="rounded-2xl bg-emerald-50 border border-emerald-100 px-5 py-4 flex items-center gap-3">
                <span class="text-xl">✅</span>
                <div>
                    <p class="font-bold text-emerald-700">Vote enregistré</p>
                    <p class="text-sm text-emerald-600">Merci d'avoir participé !</p>
                </div>
            </div>

            <!-- Formulaire de vote -->
            <template v-else>
                <div class="space-y-3" style="padding-top: 10px;">
                    <!-- Options -->
                    <button
                        v-for="opt in poll.options"
                        :key="opt.id"
                        type="button"
                        @click="toggleOption(opt.id, poll.allow_multiple_choices)"
                        class="w-full flex items-center gap-4 rounded-2xl border px-6 py-5 text-left text-lg font-semibold transition-all duration-150 cursor-pointer"
                        :class="isSelected(opt.id) ? '' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50'"
                        :style="isSelected(opt.id) ? selectedOptionStyle : {}"
                    >
                        <!-- Indicateur radio/checkbox visuel -->
                        <span
                            class="w-7 h-7 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors"
                            :class="isSelected(opt.id) ? '' : 'border-slate-300'"
                            :style="isSelected(opt.id) ? radioSelectedStyle : {}"
                        >
                            <span v-if="isSelected(opt.id)" class="w-3 h-3 rounded-full bg-white"></span>
                        </span>
                        {{ opt.label }}
                    </button>
                </div>

                <!-- Erreur vote -->
                <p v-if="voteError" class="text-base text-red-500 text-center">{{ voteError }}</p>

                <!-- Bouton voter -->
                <button
                    @click="handleVote"
                    :disabled="selectedOptions.length === 0 || loading"
                    class="w-full rounded-2xl disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer text-white font-black py-5 text-xl tracking-wide transition-all duration-150"
                    :style="buttonGradientStyle.background ? buttonGradientStyle : { background: '#6366f1' }"
                >
                    {{ hasVoted ? 'Modifier mon vote' : 'Voter' }}
                </button>
            </template>

            <!-- Lien vers les résultats si publics ou propriétaire -->
            <button
                v-if="poll.results_public || isOwner"
                @click="emit('results')"
                class="w-full text-center text-lg font-semibold transition-colors pt-2 cursor-pointer"
                :style="themeTextStyle.color ? themeTextStyle : { color: '#6366f1' }"
            >
                Voir les résultats →
            </button>
        </template>
    </div>
</template>

<style scoped>
@keyframes gradientShift {
    0%   { background-position: 0% 0%; }
    25%  { background-position: 100% 50%; }
    50%  { background-position: 50% 100%; }
    75%  { background-position: 0% 50%; }
    100% { background-position: 0% 0%; }
}
</style>
