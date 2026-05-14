<script setup>
import { ref } from 'vue';
// Composant d'édition des options — compatible v-model
const props = defineProps({
    modelValue:  { type: Array,  default: () => [] },
    accentColor: { type: String, default: '#818cf8' }, // couleur du thème actif
});
const emit = defineEmits(['update:modelValue']);

const focusedIndex = ref(null);

// Ajouter une option vide (max 6)
function addOption() {
    if (props.modelValue.length >= 5) return;
    emit('update:modelValue', [...props.modelValue, { label: '' }]);
}

// Modifier le label d'une option à l'index donné
function updateLabel(index, value) {
    const updated = props.modelValue.map((opt, i) =>
        i === index ? { ...opt, label: value } : opt
    );
    emit('update:modelValue', updated);
}

// Supprimer une option (min 2 options)
function removeOption(index) {
    if (props.modelValue.length <= 2) return;
    emit('update:modelValue', props.modelValue.filter((_, i) => i !== index));
}
</script>

<template>
    <div class="space-y-4">
        <div
            v-for="(opt, i) in modelValue"
            :key="opt.id ?? i"
            class="flex items-center gap-4"
        >
            <!-- Indicateur de numéro d'option -->
            <span class="w-11 h-11 flex items-center justify-center rounded-full text-lg font-black shrink-0"
                  :style="{ background: accentColor + '20', color: accentColor }">
                {{ i + 1 }}
            </span>

            <input
                type="text"
                :value="opt.label"
                @input="updateLabel(i, $event.target.value)"
                @focus="focusedIndex = i"
                @blur="focusedIndex = null"
                placeholder="Option..."
                class="flex-1 rounded-xl bg-white px-5 py-4 text-xl font-semibold outline-none transition-all"
                :style="{
                    border: focusedIndex === i ? `2px solid ${accentColor}` : `2px solid ${accentColor}59`,
                    color: accentColor,
                    transition: 'border-color 0.2s ease-in-out'
                }"
            />

            <!-- Bouton supprimer — désactivé si < 3 options -->
            <button
                type="button"
                @click="removeOption(i)"
                :disabled="modelValue.length <= 2"
                class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-400 text-base transition-colors"
                :class="modelValue.length > 2 ? 'hover:bg-red-50 hover:text-red-400' : 'opacity-30 cursor-not-allowed'"
            >
                ✕
            </button>
        </div>

        <!-- Ajouter une option -->
        <button
            type="button"
            @click="addOption"
            :disabled="modelValue.length >= 5"
            class="flex items-center gap-2 text-base font-semibold opacity-50 hover:opacity-100 disabled:opacity-20 disabled:cursor-not-allowed"
            :style="{ color: accentColor, marginTop: '25px', transition: 'opacity 0.2s ease-in-out' }"
        >
            <span class="text-lg leading-none">+</span> Ajouter une option
        </button>
    </div>
</template>
