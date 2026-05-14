<script setup>
import { computed } from 'vue';
import PollShareLink from './PollShareLink.vue';

const props = defineProps({
    polls: { type: Array, default: () => [] },
});

const emit = defineEmits(['create', 'edit', 'delete', 'results']);

// Calcule le statut lisible d'un sondage
function statusOf(poll) {
    if (poll.is_draft) return { label: 'Brouillon', style: 'bg-amber-50 text-amber-600 border-amber-100' };
    if (poll.ends_at && new Date(poll.ends_at) < new Date()) return { label: 'Terminé', style: 'bg-slate-100 text-slate-500 border-slate-200' };
    return { label: 'Actif', style: 'bg-emerald-50 text-emerald-600 border-emerald-100' };
}
</script>

<template>
    <div class="space-y-6">

        <!-- Header dashboard -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Mes sondages</h1>
                <p class="text-sm text-slate-400 mt-0.5">{{ polls.length }} sondage{{ polls.length !== 1 ? 's' : '' }}</p>
            </div>
            <button
                @click="emit('create')"
                class="rounded-2xl bg-indigo-500 hover:bg-indigo-600 text-white font-bold px-5 py-2.5 text-sm transition-all duration-150"
            >
                + Nouveau
            </button>
        </div>

        <!-- Empty state -->
        <div v-if="polls.length === 0" class="text-center py-20 text-slate-400">
            <p class="text-5xl mb-4">🗳</p>
            <p class="font-semibold text-slate-500">Aucun sondage pour l'instant</p>
            <p class="text-sm mt-1">Crée ton premier sondage !</p>
        </div>

        <!-- Cards -->
        <div v-else class="space-y-3">
            <div
                v-for="poll in polls"
                :key="poll.id"
                class="rounded-2xl bg-white/80 backdrop-blur-sm border border-slate-100 shadow-sm px-5 py-4 transition-all duration-150 hover:shadow-md hover:border-slate-200"
            >
                <div class="flex items-start justify-between gap-3">

                    <!-- Infos -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <!-- Badge statut -->
                            <span
                                class="text-xs font-semibold px-2 py-0.5 rounded-full border"
                                :class="statusOf(poll).style"
                            >
                                {{ statusOf(poll).label }}
                            </span>
                            <!-- Badge choix multiple -->
                            <span v-if="poll.allow_multiple_choices" class="text-xs text-violet-500 bg-violet-50 border border-violet-100 px-2 py-0.5 rounded-full font-semibold">
                                Multi
                            </span>
                        </div>
                        <!-- Question -->
                        <p class="font-bold text-slate-800 truncate">{{ poll.question }}</p>
                        <!-- Métadonnées -->
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ poll.options?.length ?? 0 }} option{{ poll.options?.length !== 1 ? 's' : '' }}
                            <template v-if="poll.ends_at">
                                · fin le {{ new Date(poll.ends_at).toLocaleDateString('fr-CH') }}
                            </template>
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-1 shrink-0">
                        <button @click="emit('results', poll)" class="w-8 h-8 flex items-center justify-center rounded-xl text-base transition-colors duration-150 text-indigo-500 hover:bg-indigo-50" title="Résultats">
                            📊
                        </button>
                        <button @click="emit('edit', poll)" class="w-8 h-8 flex items-center justify-center rounded-xl text-base transition-colors duration-150 text-slate-500 hover:bg-slate-100" title="Modifier">
                            ✏️
                        </button>
                        <button @click="emit('delete', poll)" class="w-8 h-8 flex items-center justify-center rounded-xl text-base transition-colors duration-150 text-red-400 hover:bg-red-50" title="Supprimer">
                            🗑
                        </button>
                    </div>
                </div>

                <!-- Lien de partage (visible uniquement si sondage lancé) -->
                <div v-if="!poll.is_draft && poll.secret_token" class="mt-3">
                    <PollShareLink :token="poll.secret_token" />
                </div>
            </div>
        </div>
    </div>
</template>

