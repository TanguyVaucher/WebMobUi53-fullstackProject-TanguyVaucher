import { ref } from 'vue';
import { useFetchApi } from './useFetchApi';

/**
 * Gère le CRUD des sondages du user connecté.
 * Expose : polls, loading, error, et les fonctions fetchPolls, createPoll, updatePoll, deletePoll.
 */
export function usePolls() {
    const { fetchApi } = useFetchApi();

    const polls   = ref([]);
    const loading = ref(false);
    const error   = ref(null);

    // Charge la liste des sondages du user
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

    // Crée un sondage, retourne le sondage créé
    async function createPoll(data) {
        const poll = await fetchApi({ url: '/polls', data });
        // On l'ajoute en tête de liste sans recharger
        polls.value.unshift(poll);
        return poll;
    }

    // Met à jour un sondage, retourne le sondage mis à jour
    async function updatePoll(id, data) {
        const updated = await fetchApi({ url: `/polls/${id}`, data, method: 'PATCH' });
        // Remplacer dans la liste locale
        const idx = polls.value.findIndex(p => p.id === id);
        if (idx !== -1) polls.value[idx] = updated;
        return updated;
    }

    // Supprime un sondage
    // Note : Laravel retourne 204 No Content (sans body JSON), donc fetchApi
    // va rejeter la promesse malgré le succès. On catch le 204 et on continue.
    async function deletePoll(id) {
        try {
            await fetchApi({ url: `/polls/${id}`, method: 'DELETE' });
        } catch (err) {
            if (err?.status !== 204) throw err;
        }
        // Mise à jour réactive — retire le sondage de la liste sans recharger
        polls.value = polls.value.filter(p => p.id !== id);
    }

    return { polls, loading, error, fetchPolls, createPoll, updatePoll, deletePoll };
}
