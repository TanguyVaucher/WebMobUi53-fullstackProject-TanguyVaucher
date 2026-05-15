<script setup>
import { ref } from 'vue';
// Paramètres du sondage — compatible v-model sur un objet settings
const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({
            allow_multiple_choices: false,
            allow_vote_change:      false,
            results_public:         false,
            duration:               null,
            is_draft:               true,
        }),
    },
});
const emit = defineEmits(['update:modelValue', 'depublish']);

// Met à jour un champ dans l'objet settings
function update(key, value) {
    emit('update:modelValue', { ...props.modelValue, [key]: value });
}

// Convertit les minutes (UI) en secondes (API)
function durationMinutes() {
    return props.modelValue.duration ? Math.floor(props.modelValue.duration / 60) : '';
}
function setDuration(minutes) {
    const secs = minutes ? parseInt(minutes) * 60 : null;
    update('duration', secs);
}

const multiplier = ref(1);
</script>

<template>
    <div class="space-y-5">
        <!-- Toggles booléens -->
        <label class="flex items-center justify-between cursor-pointer">
            <span class="text-base font-medium text-slate-700">Choix multiple</span>
            <button
                type="button"
                @click="update('allow_multiple_choices', !modelValue.allow_multiple_choices)"
                class="relative w-14 h-7 rounded-full transition-colors duration-200 shrink-0"
                :class="modelValue.allow_multiple_choices ? 'bg-indigo-400' : 'bg-slate-200'"
            >
                <span class="absolute top-1 w-5 h-5 rounded-full bg-white shadow transition-all duration-200"
                      :style="{ left: modelValue.allow_multiple_choices ? '32px' : '4px' }"></span>
            </button>
        </label>

        <label class="flex items-center justify-between cursor-pointer">
            <span class="text-base font-medium text-slate-700">Résultats publics</span>
            <button
                type="button"
                @click="update('results_public', !modelValue.results_public)"
                class="relative w-14 h-7 rounded-full transition-colors duration-200 shrink-0"
                :class="modelValue.results_public ? 'bg-indigo-400' : 'bg-slate-200'"
            >
                <span class="absolute top-1 w-5 h-5 rounded-full bg-white shadow transition-all duration-200"
                      :style="{ left: modelValue.results_public ? '32px' : '4px' }"></span>
            </button>
        </label>

        <label class="flex items-center justify-between cursor-pointer">
            <span class="text-base font-medium text-slate-700">Modifier le vote</span>
            <button
                type="button"
                @click="update('allow_vote_change', !modelValue.allow_vote_change)"
                class="relative w-14 h-7 rounded-full transition-colors duration-200 shrink-0"
                :class="modelValue.allow_vote_change ? 'bg-indigo-400' : 'bg-slate-200'"
            >
                <span class="absolute top-1 w-5 h-5 rounded-full bg-white shadow transition-all duration-200"
                      :style="{ left: modelValue.allow_vote_change ? '32px' : '4px' }"></span>
            </button>
        </label>

        <!-- Durée en minutes -->
        <div class="space-y-2">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <span class="text-base font-medium text-slate-700">Durée (en min)</span>
                <div class="flex items-center gap-1.5 flex-wrap">
                    <!-- Multiplicateur chevrons -->
                    <div class="flex flex-col items-center">
                        <button type="button" @click="multiplier = Math.min(9, multiplier + 1)"
                            class="text-slate-400 hover:text-slate-700 transition-colors leading-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
                        </button>
                        <button type="button" @click="multiplier = Math.max(1, multiplier - 1)"
                            class="text-slate-400 hover:text-slate-700 transition-colors leading-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                    </div>
                    <button type="button" @click="setDuration(60 * multiplier)"
                        class="rounded-lg px-2.5 py-1 text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-indigo-100 hover:text-indigo-600 transition-colors w-[4.5rem] text-center">{{ multiplier }} heure{{ multiplier > 1 ? 's' : '' }}</button>
                    <button type="button" @click="setDuration(1440 * multiplier)"
                        class="rounded-lg px-2.5 py-1 text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-indigo-100 hover:text-indigo-600 transition-colors w-[4.5rem] text-center">{{ multiplier }} jour{{ multiplier > 1 ? 's' : '' }}</button>
                    <button type="button" @click="setDuration(10080 * multiplier)"
                        class="rounded-lg px-2.5 py-1 text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-indigo-100 hover:text-indigo-600 transition-colors w-[4.5rem] text-center">{{ multiplier }} sem.</button>
                    <button type="button" @click="setDuration(43200 * multiplier)"
                        class="rounded-lg px-2.5 py-1 text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-indigo-100 hover:text-indigo-600 transition-colors w-[4.5rem] text-center">{{ multiplier }} mois</button>
                </div>
            </div>
            <div class="relative flex items-stretch">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-800">
                    <path d="M5 22h14"/><path d="M5 2h14"/>
                    <path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22"/>
                    <path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"/>
                </svg>
                <input
                    type="number"
                    min="1"
                    placeholder="N minutes"
                    :value="durationMinutes()"
                    @input="setDuration($event.target.value)"
                    class="flex-1 rounded-xl border border-slate-200 bg-white pl-9 pr-3 py-2 text-base outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all"
                />
                <!-- + / - -->
                <div class="flex flex-col gap-1 ml-2">
                    <button type="button"
                        @click="setDuration((durationMinutes() || 0) + 1)"
                        class="flex-1 flex items-center justify-center rounded-md px-2 transition-all duration-200 ease-in-out"
                        style="background: rgba(129,140,248,0.50)"
                        @mouseenter="$event.currentTarget.style.background='rgba(129,140,248,1)'"
                        @mouseleave="$event.currentTarget.style.background='rgba(129,140,248,0.50)'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <button type="button"
                        @click="setDuration(Math.max(1, (durationMinutes() || 1) - 1))"
                        class="flex-1 flex items-center justify-center rounded-md px-2 transition-all duration-200 ease-in-out"
                        style="background: rgba(129,140,248,0.20)"
                        @mouseenter="$event.currentTarget.style.background='rgba(129,140,248,1)'"
                        @mouseleave="$event.currentTarget.style.background='rgba(129,140,248,0.20)'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Lancer le sondage (brouillon → actif) -->
        <div v-if="modelValue.is_draft" class="pt-3 border-t border-slate-100">
            <label class="flex items-center justify-between cursor-pointer">
                <div>
                    <p class="text-lg font-medium text-slate-800">Lancer maintenant</p>
                    <p class="text-base text-slate-400">Le sondage sera ouvert au vote</p>
                </div>
                <button
                    type="button"
                    @click="update('is_draft', false)"
                    class="rounded-xl bg-emerald-100 text-emerald-700 px-4 py-2 text-sm font-semibold hover:bg-emerald-200 transition-colors"
                >
                    Lancer
                </button>
            </label>
        </div>

        <div v-else class="pt-3 border-t border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2 text-emerald-600 text-lg font-medium">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
                Sondage actif
            </div>
            <button
                type="button"
                @click="emit('depublish')"
                class="rounded-xl bg-red-100 text-red-600 px-4 py-2 text-sm font-semibold hover:bg-red-200 transition-colors opacity-60 hover:opacity-100"
            >
                Dépublier
            </button>
        </div>
    </div>
</template>
