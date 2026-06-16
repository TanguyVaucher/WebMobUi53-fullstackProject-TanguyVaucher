// Composable qui charge les résultats d’un sondage via son token depuis l’API et expose les données, l’état de chargement et les erreurs.
// Utilisé avec usePolling pour le refresh automatique.

import { ref } from 'vue';
import { useFetchApi } from './useFetchApi';

export function usePollResults() {
    const { fetchApi } = useFetchApi();

    const results     = ref(null);   // { poll, options, total_votes, expired }
    const loading     = ref(false);
    const error       = ref(null);

    // Récupère les résultats via l'API
    async function fetchResults(token) {
        loading.value = true;
        error.value   = null;
        try {
            results.value = await fetchApi({ url: `/polls/token/${token}/results` });
        } catch (err) {
            error.value = err?.data?.message || 'Résultats indisponibles.';
        } finally {
            loading.value = false;
        }
    }

    return { results, loading, error, fetchResults };
}
