<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import PollShareLink                  from './PollShareLink.vue';
import { POLL_COLORS } from '../utils/pollColors';

defineProps({
    polls: { type: Array, default: () => [] },
});
const emit = defineEmits(['create', 'edit', 'delete', 'results', 'publish']);

function statusOf(poll) {
    if (poll.is_draft)
        return { label: 'Brouillon',      dot: 'bg-slate-400',               badge: 'text-slate-600' };
    if (poll.ends_at && new Date(poll.ends_at) < new Date())
        return { label: 'Sondage terminé', dot: 'bg-red-400',                badge: 'text-red-600' };
    return   { label: 'Sondage actif',    dot: 'bg-emerald-500 animate-pulse', badge: 'text-emerald-700' };
}

// Horloge réactive à la seconde pour le compte à rebours live
const now = ref(Date.now());
let ticker;
onMounted(() => { ticker = setInterval(() => now.value = Date.now(), 1000); });
onUnmounted(() => clearInterval(ticker));

function timeLeft(poll) {
    if (!poll.ends_at) return null;
    const diff = Math.max(0, new Date(poll.ends_at) - now.value);
    if (diff <= 0) return null;
    const totalSec = Math.floor(diff / 1000);
    const sec = totalSec % 60;
    const totalMin = Math.floor(totalSec / 60);
    const min = totalMin % 60;
    const totalH = Math.floor(totalMin / 60);
    const h   = totalH % 24;
    const d   = Math.floor(totalH / 24);
    const ss  = String(sec).padStart(2, '0');
    if (d > 0)        return `Il reste ${d}j ${h}h ${min} min`;
    if (h > 0)        return `Il reste ${h}h ${min} min ${ss} s`;
    if (totalMin > 0) return `Il reste ${min} min ${ss} s`;
    return `Il reste ${ss} s`;
}

function canShare(poll) {
    return !poll.is_draft
        && poll.secret_token
        && (!poll.ends_at || new Date(poll.ends_at) > now.value);
}

// Génère un style de header unique par card :
// - avec thème  → dégradé animé, vitesse + délai + angle uniques basés sur l'id
// - sans thème  → gris neutre (anciens sondages sans couleur définie)
function headerStyle(poll) {
    if (poll.is_draft)
        return { background: '#cbd5e1' }; // slate-300 aplat

    if (poll.color && POLL_COLORS[poll.color]) {
        const c = POLL_COLORS[poll.color];

        const duration = 4 + (poll.id % 5);
        const delay    = -((poll.id * 1.3) % duration);
        const angle    = 120 + (poll.id * 37) % 120;

        return {
            background: `linear-gradient(${angle}deg, ${c.from}, ${c.to}, ${c.via}, ${c.from})`,
            backgroundSize: '400% 400%',
            animation: `gradientShift ${duration}s ease infinite`,
            animationDelay: `${delay}s`,
        };
    }
    return { background: '#e2e8f0' }; // slate-200 neutre
}
</script>

<template>
    <div class="p-6">

        <!-- Empty state -->
        <div v-if="polls.length === 0"
             class="flex flex-col items-center justify-center h-64 text-center gap-4 mt-20">
            <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-indigo-100 to-violet-100
                        flex items-center justify-center text-4xl">😢</div>
            <div>
                <p class="font-black text-slate-800 text-5xl tracking-tight">Aucun sondage.</p>
                <p class="text-slate-400 text-2xl mt-2">Créez le premier !</p>
            </div>
            <button @click="emit('create')"
                    class="rounded-2xl bg-indigo-500 hover:bg-indigo-600 text-white
                           font-bold px-6 py-3 text-sm transition-all duration-150 mt-2.5">
                + Nouveau sondage
            </button>
        </div>

        <!-- Grille 4 colonnes fixes, chaque card = ~25vw -->
        <div v-else class="grid gap-6"
             style="grid-template-columns: repeat(4, 1fr)">

            <div
                v-for="poll in polls"
                :key="poll.id"
                class="group relative flex flex-col rounded-3xl bg-white border border-slate-100
                       shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-200
                       overflow-hidden cursor-pointer"
                @click="emit('edit', poll)"
            >
                <!-- Header coloré (animé si thème, solide sinon) -->
                <div class="h-40 shrink-0 flex items-end p-4 relative"
                     :style="headerStyle(poll)">

                    <!-- Compte à rebours centré — sondages actifs avec durée -->
                    <div v-if="!poll.is_draft && timeLeft(poll)"
                         class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span class="text-white font-black text-2xl drop-shadow-lg leading-none tracking-tight">
                            {{ timeLeft(poll) }}
                        </span>
                    </div>

                    <!-- Badge statut -->
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1
                                 bg-white/60 backdrop-blur-md text-xs font-bold"
                          :class="statusOf(poll).badge">
                        <span class="w-2.5 h-2.5 rounded-full" :class="statusOf(poll).dot"></span>
                        {{ statusOf(poll).label }}
                    </span>
                </div>

                <!-- Corps -->
                <div class="flex flex-col flex-1 px-4 pt-5 pb-7 gap-3">
                    <p class="font-black text-xl leading-snug line-clamp-2 mb-2" style="letter-spacing: 0.4px; color: rgba(0,0,0,0.9)">
                        {{ poll.question }}
                    </p>
                    <p class="text-xs text-slate-400 font-medium">
                        Créé le {{ new Date(poll.created_at).toLocaleDateString('fr-CH', { day: '2-digit', month: 'short', year: 'numeric' }) }}
                        à {{ new Date(poll.created_at).toLocaleTimeString('fr-CH', { hour: '2-digit', minute: '2-digit' }) }}
                    </p>


                    <!-- Lien partage si sondage actif et non terminé -->
                    <div v-if="canShare(poll)" class="mt-1 mb-3" @click.stop>
                        <PollShareLink :token="poll.secret_token" />
                    </div>
                </div>

                <!-- Actions toujours visibles -->
                <div class="grid grid-cols-3 border-t border-slate-100 divide-x divide-slate-100"
                     @click.stop>
                    <!-- Brouillon → Publier | Actif → Résultats -->
                    <button v-if="poll.is_draft"
                            @click="emit('publish', poll)"
                            class="py-2.5 text-xs font-semibold text-emerald-600
                                   hover:bg-emerald-50 transition-colors flex items-center justify-center gap-1">
                        Publier
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12l5 5L20 7"/>
                        </svg>
                    </button>
                    <button v-else
                            @click="emit('results', poll)"
                            class="py-2.5 text-xs font-semibold text-indigo-500
                                   hover:bg-indigo-50 transition-colors flex items-center justify-center">
                        Résultats
                    </button>
                    <button @click="emit('edit', poll)"
                            class="py-2.5 text-xs font-semibold text-slate-500
                                   hover:bg-slate-50 transition-colors flex items-center justify-center">
                        Modifier
                    </button>
                    <button @click="emit('delete', poll)"
                            class="py-2.5 text-xs font-semibold text-red-400
                                   hover:bg-red-50 transition-colors flex items-center justify-center">
                        🗑
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@keyframes gradientShift {
    0%   { background-position: 0% 0%; }
    25%  { background-position: 100% 50%; }
    50%  { background-position: 50% 100%; }
    75%  { background-position: 0% 50%; }
    100% { background-position: 0% 0%; }
}
</style>
