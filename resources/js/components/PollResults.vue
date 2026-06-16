<script setup>
import { onMounted } from 'vue';
import { usePollResults } from '../composables/usePollResults';
import { usePolling } from '../composables/usePolling';

const props = defineProps({
    token: { type: String, required: true },
    hasBack: { type: Boolean, default: false },
    backLabel: { type: String, default: '← Retour au sondage' },
});
const emit = defineEmits(['back', 'poll-loaded']);

const { results, loading, error, fetchResults } = usePollResults();

// Charge les résultats immédiatement au montage
onMounted(async () => {
    await fetchResults(props.token);
    if (results.value?.poll) emit('poll-loaded', results.value.poll);
});

// Rafraîchit automatiquement les résultats toutes les 5s (polling)
// usePolling s'arrête proprement via onUnmounted (voir composable)
usePolling(() => fetchResults(props.token), 5000);

// Génère une couleur pastel pour chaque barre (cycle de 5 couleurs)
const barColors = [
    'bg-indigo-400', 'bg-violet-400', 'bg-sky-400', 'bg-pink-400', 'bg-teal-400'
];
function barColor(index) {
    return barColors[index % barColors.length];
}
</script>

<template>
    <div class="space-y-6">

        <!-- Chargement initial -->
        <div v-if="loading && !results" class="text-center py-16 text-slate-400">
            <div class="w-8 h-8 border-2 border-indigo-300 border-t-indigo-500 rounded-full animate-spin mx-auto mb-3">
            </div>
            Chargement...
        </div>

        <!-- Erreur -->
        <div v-else-if="error" class="rounded-2xl bg-red-50 border border-red-100 px-6 py-8 text-center">
            <p class="font-bold text-red-600">{{ error }}</p>
        </div>

        <template v-else-if="results">

            <!-- Bouton retour -->
            <button v-if="hasBack" @click="emit('back')" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500
                       hover:text-indigo-600 transition-colors duration-150">
                {{ backLabel }}
            </button>

            <!-- Box résultats -->
            <div class="bg-white rounded-3xl shadow-md border border-slate-100 p-8 space-y-8">

                <!-- Total votes + statut -->
                <div class="flex items-center justify-between">
                    <p class="text-slate-500">
                        <span class="font-bold text-slate-800 text-5xl">{{ results.total_votes }}</span>
                        <span class="text-2xl ml-2">Votes</span>
                    </p>

                    <!-- Label Live (vert) -->
                    <span v-if="!results.expired" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                 text-sm font-bold text-emerald-600 bg-emerald-50 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live
                    </span>

                    <!-- Label Terminé (rouge) -->
                    <span v-else class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                 text-sm font-bold text-red-500 bg-red-50 border border-red-200">
                        <span class="w-2 h-2 rounded-full bg-red-400"></span>
                        Terminé
                    </span>
                </div>

                <!-- Graphique barres CSS -->
                <div class="space-y-5">
                    <div v-for="(opt, i) in results.options" :key="opt.id" class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-slate-800 text-base truncate pr-4">{{ opt.label }}</span>
                            <span class="text-slate-500 font-bold text-base shrink-0">
                                {{ opt.votes_count }} · {{ opt.percentage }}%
                            </span>
                        </div>
                        <!-- Barre de progression CSS -->
                        <div class="h-4 w-full rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700 ease-out" :class="barColor(i)"
                                :style="{ width: opt.percentage + '%' }"></div>
                        </div>
                    </div>
                </div>

            </div>
        </template>
    </div>
</template>
