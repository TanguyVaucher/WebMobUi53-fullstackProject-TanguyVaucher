// Composable qui gère le CRUD des sondages du dashboard via l’API.
// Utilisé par AppPollDashboard.vue et PollEditor.vue

import { ref } from 'vue';
import { useFetchApi } from './useFetchApi';

export function usePolls() {
    const { fetchApi } = useFetchApi();

    const polls   = ref([]);
    const loading = ref(false);
    const error   = ref(null);

    // Charger la liste des sondages du user
    async function fetchPolls() {
        loading.value = true;
        error.value   = null;
        try {
            polls.value = await fetchApi({ url: '/polls' });
        } catch (err) {
            error.value = err?.data?.message || 'Impossible de charger les sondages.';
        } finally {
            loading.value = false;
        }
    }

    // Créer un sondage
    async function createPoll(data) {
        const poll = await fetchApi({ url: '/polls', data });
        // L'ajouter en tête du tableau des polls sans recharger grâce à ref
        polls.value.unshift(poll);
        return poll;
    }

    // Updater un sondage
    async function updatePoll(id, data) {
        // Màj du sondage
        const updated = await fetchApi({ url: `/polls/${id}`, data, method: 'PATCH' });
        // Remplacer dans le tableau local
        const idx = polls.value.findIndex(p => p.id === id);
        if (idx !== -1) polls.value[idx] = updated; // Si existant
        return updated;
    }

    // Supprimer un sondage
    async function deletePoll(id) {
        try {
            await fetchApi({ url: `/polls/${id}`, method: 'DELETE' });
        } catch (err) {
            // Laravel renvoie 204 No Content après la suppression (le sondage est bien supprimé, mais la réponse est vide)
            if (err?.status !== 204) throw err;
        }
        // Retirer le sondage du tableau local avec filter (nouveau tableau sans l'id supprimé)
        polls.value = polls.value.filter(p => p.id !== id);
    }

    return { polls, loading, error, fetchPolls, createPoll, updatePoll, deletePoll };
}
