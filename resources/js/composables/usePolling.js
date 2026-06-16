// Relance automatiquement le chargement des résultats d’un sondage toutes les 5 secondes tant que la page de résultats est affichée (PollResults.vue).
// onMounted démarre l’intervalle quand on est dans les résultats, onUnmounted l’arrête quand on quitte cette vue.

import { onMounted, onUnmounted } from 'vue';

/**
 * Polling composable
 *
 * @param {Function} fn - The function to call on each interval tick
 * @param {number} [interval=5000] - The interval in milliseconds
 */
export function usePolling(fn, interval = 5000) {
  let timer;

  onMounted(() => {
    timer = setInterval(fn, interval);
  });

  onUnmounted(() => clearInterval(timer));
}
