/**
 * Palette de couleurs disponibles pour les sondages.
 * Chaque couleur a un gradient animé (deux teintes proches) et une couleur solide fallback.
 */
export const POLL_COLORS = {
    indigo: {
        label:  'Indigo',   // même en français
        solid:  '#818cf8',           // teinte unique si pas d'anim
        from:   '#818cf8',           // indigo-400
        via:    '#6366f1',           // indigo-500
        to:     '#a5b4fc',           // indigo-300 (proche)
    },
    violet: {
        label:  'Violet',   // même en français
        solid:  '#a78bfa',
        from:   '#a78bfa',           // violet-400
        via:    '#8b5cf6',           // violet-500
        to:     '#c4b5fd',           // violet-300
    },
    sky: {
        label:  'Ciel',
        solid:  '#38bdf8',
        from:   '#38bdf8',           // sky-400
        via:    '#0ea5e9',           // sky-500
        to:     '#7dd3fc',           // sky-300
    },
    teal: {
        label:  'Turquoise',
        solid:  '#2dd4bf',
        from:   '#2dd4bf',           // teal-400
        via:    '#14b8a6',           // teal-500
        to:     '#5eead4',           // teal-300
    },
    pink: {
        label:  'Rose',
        solid:  '#f472b6',
        from:   '#f472b6',           // pink-400
        via:    '#ec4899',           // pink-500
        to:     '#f9a8d4',           // pink-300
    },
    orange: {
        label:  'Orange',   // même en français
        solid:  '#fb923c',
        from:   '#fb923c',           // orange-400
        via:    '#f97316',           // orange-500
        to:     '#fdba74',           // orange-300
    },
};

// Couleurs fallback aléatoires pour les sondages sans thème (basées sur l'id)
const FALLBACK_SOLIDS = [
    '#94a3b8', // slate-400
    '#a3a3a3', // neutral-400
    '#a8a29e', // stone-400
    '#86efac', // green-300
    '#93c5fd', // blue-300
    '#d8b4fe', // purple-300
];

// Retourne une couleur solide stable basée sur l'id du sondage
export function fallbackColor(pollId) {
    return FALLBACK_SOLIDS[pollId % FALLBACK_SOLIDS.length];
}
