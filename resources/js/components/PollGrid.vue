<!-- Composant d'affichage du dashboard -->

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import PollShareLink from './PollShareLink.vue';
import { POLL_COLORS } from '../utils/pollColors';

// Reçoit depuis l'app vue la liste des sondages de l'utilisateur
defineProps({
    polls: { type: Array, default: () => [] },
});

// Événements à envoyer à l'app vue lors des actions utilisateur
const emit = defineEmits(['create', 'edit', 'delete', 'results', 'publish']);

// Badge de statut d'un sondage
function statusOf(poll) {
    if (poll.is_draft) return { label: 'Brouillon', dot: 'bg-slate-400', badge: 'text-slate-600' }; // Brouillon
    if (poll.ends_at && new Date(poll.ends_at) < new Date()) return { label: 'Sondage terminé', dot: 'bg-red-400', badge: 'text-red-600' }; // Terminé
    return { label: 'Sondage actif', dot: 'bg-emerald-500 animate-pulse', badge: 'text-emerald-700' }; // Actif
}

// Compte à rebours à la seconde pour le compte à rebours live (horloge réactive)
const now = ref(Date.now());
let ticker;
// Démarre le timer à l’affichage du dashboard, puis l’arrête quand on quitte cette vue
onMounted(() => { ticker = setInterval(() => now.value = Date.now(), 1000); });
onUnmounted(() => clearInterval(ticker));

// Calculer le temps restant d'un sondage actif
function timeLeft(poll) {
    if (!poll.ends_at) return null;
    const diff = Math.max(0, new Date(poll.ends_at) - now.value);
    if (diff <= 0) return null;
    const totalSec = Math.floor(diff / 1000);
    const sec = totalSec % 60;
    const totalMin = Math.floor(totalSec / 60);
    const min = totalMin % 60;
    const totalH = Math.floor(totalMin / 60);
    const h = totalH % 24;
    const d = Math.floor(totalH / 24);
    const ss = String(sec).padStart(2, '0');
    if (d > 0) return `Il reste ${d}j ${h}h ${min} min`; // Affichage jours
    if (h > 0) return `Il reste ${h}h ${min} min ${ss} s`; // Affichage heure
    if (totalMin > 0) return `Il reste ${min} min ${ss} s`; // Affichage min
    return `Il reste ${ss} s`; // Affichage sec
}

// Affichage du lien de partage uniquement si le sondage est actif
function canShare(poll) {
    return !poll.is_draft
        && poll.secret_token
        && (!poll.ends_at || new Date(poll.ends_at) > now.value);
}

// Thème de couleur animé dans le header des polls (gris simple pour les brouillons)
function headerStyle(poll) {
    if (poll.is_draft)
        return { background: '#cbd5e1' };

    if (poll.color && POLL_COLORS[poll.color]) {
        const c = POLL_COLORS[poll.color];

        const duration = 4 + (poll.id % 5);
        const delay = -((poll.id * 1.3) % duration);
        const angle = 120 + (poll.id * 37) % 120;

        return {
            background: `linear-gradient(${angle}deg, ${c.from}, ${c.to}, ${c.via}, ${c.from})`,
            backgroundSize: '400% 400%',
            animation: `gradientShift ${duration}s ease infinite`,
            animationDelay: `${delay}s`,
        };
    }
    return { background: '#e2e8f0' }; // Valeur de secours
}
</script>

<template>
    <div class="p-6">

        <!-- Affichage si aucun poll existant -->
        <div v-if="polls.length === 0" class="flex flex-col items-center justify-center h-64 text-center gap-4 mt-20">
            <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-indigo-100 to-violet-100
                        flex items-center justify-center text-4xl">😢</div>
            <div>
                <p class="font-black text-slate-800 text-5xl tracking-tight">Aucun sondage.</p>
                <p class="text-slate-400 text-2xl mt-2">Créez le premier !</p>
            </div>
            <button @click="emit('create')" class="rounded-2xl bg-indigo-500 hover:bg-indigo-600 text-white
                           font-bold px-6 py-3 text-sm transition-all duration-150 mt-2.5">
                + Nouveau sondage
            </button>
        </div>

        <!-- Affichage normal -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

            <!-- Chargement des polls -->
            <div v-for="poll in polls" :key="poll.id" class="group relative flex flex-col rounded-3xl bg-white border border-slate-100
                       shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-200
                       overflow-hidden cursor-pointer" @click="emit('edit', poll)">

                <!-- Header dégradé -->
                <div class="h-40 shrink-0 flex items-end p-4 relative" :style="headerStyle(poll)">

                    <!-- Compte à rebours si actif -->
                    <div v-if="!poll.is_draft && timeLeft(poll)"
                        class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span class="text-white font-black text-2xl drop-shadow-lg leading-none tracking-tight">
                            {{ timeLeft(poll) }}
                        </span>
                    </div>

                    <!-- Badge de statut -->
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1
                                 bg-white/60 backdrop-blur-md text-xs font-bold" :class="statusOf(poll).badge">
                        <span class="w-2.5 h-2.5 rounded-full" :class="statusOf(poll).dot"></span>
                        {{ statusOf(poll).label }}
                    </span>
                </div>

                <!-- Question et date de création du sondage -->
                <div class="flex flex-col flex-1 px-4 pt-5 pb-7 gap-3">
                    <p class="font-black text-xl leading-snug line-clamp-2 mb-2"
                        style="letter-spacing: 0.4px; color: rgba(0,0,0,0.9)">
                        {{ poll.question }}
                    </p>
                    <p class="text-xs text-slate-400 font-medium">
                        Créé le {{ new Date(poll.created_at).toLocaleDateString('fr-CH', {
                            day: '2-digit', month:
                                'short', year: 'numeric' }) }}
                        à {{ new Date(poll.created_at).toLocaleTimeString('fr-CH', {
                            hour: '2-digit', minute: '2-digit'
                        }) }}
                    </p>

                    <!-- Lien de partage (si sondage actif et non terminé -->
                    <div v-if="canShare(poll)" class="mt-1 mb-3" @click.stop>
                        <PollShareLink :token="poll.secret_token" />
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="grid grid-cols-3 border-t border-slate-100 divide-x divide-slate-100" @click.stop>

                    <!-- Publier (si brouillon) -->
                    <button v-if="poll.is_draft" @click="emit('publish', poll)" class="py-2.5 text-xs font-semibold text-emerald-600
                                   hover:bg-emerald-50 transition-colors flex items-center justify-center gap-1">
                        Publier
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12l5 5L20 7" />
                        </svg>
                    </button>

                    <!-- Afficher les résultats (si publié) -->
                    <button v-else @click="emit('results', poll)" class="py-2.5 text-xs font-semibold text-indigo-500
                                   hover:bg-indigo-50 transition-colors flex items-center justify-center">
                        Résultats
                    </button>

                    <!-- Modifier -->
                    <button @click="emit('edit', poll)" class="py-2.5 text-xs font-semibold text-slate-500
                                   hover:bg-slate-50 transition-colors flex items-center justify-center">
                        Modifier
                    </button>

                    <!-- Supprimer -->
                    <button @click="emit('delete', poll)" class="py-2.5 text-xs font-semibold text-red-400
                                   hover:bg-red-50 transition-colors flex items-center justify-center">
                        🗑
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* Animation du dégradé */
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
