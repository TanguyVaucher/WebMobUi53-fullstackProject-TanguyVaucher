<script setup>
import { onMounted } from 'vue';
import { usePollVoting } from '../composables/usePollVoting';

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
            <!-- En-tête -->
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight leading-snug">
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
                <div class="space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        {{ hasVoted ? 'Modifier votre vote' : (poll.allow_multiple_choices ? 'Plusieurs choix possibles' : 'Un seul choix') }}
                    </p>

                    <!-- Options -->
                    <button
                        v-for="opt in poll.options"
                        :key="opt.id"
                        type="button"
                        @click="toggleOption(opt.id, poll.allow_multiple_choices)"
                        class="w-full flex items-center gap-3 rounded-2xl border px-4 py-3 text-left transition-all duration-150"
                        :class="isSelected(opt.id)
                            ? 'border-indigo-400 bg-indigo-50 text-indigo-700 font-semibold shadow-sm'
                            : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50'"
                    >
                        <!-- Indicateur radio/checkbox visuel -->
                        <span
                            class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors"
                            :class="isSelected(opt.id) ? 'border-indigo-400 bg-indigo-400' : 'border-slate-300'"
                        >
                            <span v-if="isSelected(opt.id)" class="w-2 h-2 rounded-full bg-white"></span>
                        </span>
                        {{ opt.label }}
                    </button>
                </div>

                <!-- Erreur vote -->
                <p v-if="voteError" class="text-sm text-red-500 text-center">{{ voteError }}</p>

                <!-- Bouton voter -->
                <button
                    @click="handleVote"
                    :disabled="selectedOptions.length === 0 || loading"
                    class="w-full rounded-2xl bg-indigo-500 hover:bg-indigo-600 disabled:opacity-40 text-white font-bold py-3 text-sm tracking-wide transition-all duration-150"
                >
                    {{ hasVoted ? 'Modifier mon vote' : 'Voter' }}
                </button>
            </template>

            <!-- Lien vers les résultats si publics ou propriétaire -->
            <button
                v-if="poll.results_public || isOwner"
                @click="emit('results')"
                class="w-full text-center text-sm text-indigo-500 hover:text-indigo-700 font-medium transition-colors pt-2"
            >
                Voir les résultats →
            </button>
        </template>
    </div>
</template>
