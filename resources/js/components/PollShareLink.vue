<script setup>
import { ref } from 'vue';

const props = defineProps({
    token: { type: String, required: true },
});

// Construit l'URL de partage complète à partir du token
const shareUrl = `${window.location.origin}/polls/vote/${props.token}`;
const copied   = ref(false);

async function copyLink() {
    await navigator.clipboard.writeText(shareUrl);
    copied.value = true;
    // Réinitialise le message après 2 secondes
    setTimeout(() => (copied.value = false), 2000);
}
</script>

<template>
    <div class="flex items-center rounded-xl bg-slate-50 border border-slate-200 px-4 py-3" style="gap: 4px">
        <!-- URL tronquée pour ne pas déborder -->
        <span class="flex-1 truncate text-sm text-slate-500 font-mono">{{ shareUrl }}</span>

        <button
            type="button"
            @click="copyLink"
            class="shrink-0 rounded-lg px-3 py-1.5 text-xs font-semibold transition-all duration-150"
            :class="copied
                ? 'bg-emerald-100 text-emerald-600'
                : 'bg-indigo-100 text-indigo-600 hover:bg-indigo-200'"
        >
            {{ copied ? 'Copié !' : 'Partager' }}
        </button>

        <!-- Ouvre le sondage dans un nouvel onglet -->
        <a
            :href="shareUrl"
            target="_blank"
            class="shrink-0 w-7 h-7 flex items-center justify-center rounded-lg
                   bg-slate-200 hover:bg-indigo-100 text-slate-500 hover:text-indigo-500 transition-colors duration-150"
            title="Voir le sondage"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
        </a>
    </div>
</template>
