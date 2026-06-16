// Composable qui gère la logique de vote d’un sondage via son token
// Utilisé par PollVote.vue

import { ref } from 'vue';
import { useFetchApi } from './useFetchApi';

export function usePollVoting() {
    const { fetchApi } = useFetchApi();

    const poll            = ref(null);   // données du sondage
    const isOwner         = ref(false);  // true si le user connecté est le créateur
    const hasVoted        = ref(false);  // true si déjà voté
    const expired         = ref(false);  // true si la date de fin est dépassée
    const selectedOptions = ref([]);     // ids des options sélectionnées
    const loading         = ref(false);
    const error           = ref(null);
    const voteError       = ref(null);

    // Charger le sondage public via son token
    async function fetchPollByToken(token) {
        loading.value = true;
        error.value   = null;
        try {
            const res   = await fetchApi({ url: `/polls/token/${token}` });
            poll.value  = res.poll;
            isOwner.value  = res.isOwner;
            hasVoted.value = res.hasVoted;
            selectedOptions.value = res.userVoteOptionIds ?? [];
            expired.value  = res.expired;
        } catch (err) {
            error.value = err?.data?.message || 'Sondage introuvable.';
        } finally {
            loading.value = false;
        }
    }

    // Soumettre le vote
    async function submitVote(token) {
        voteError.value = null;
        try {
            await fetchApi({
                url:  `/polls/token/${token}/vote`,
                data: { option_ids: selectedOptions.value },
            });
            hasVoted.value = true;
            return true;
        } catch (err) {
            voteError.value = err?.data?.message || 'Impossible de voter.';
            return false;
        }
    }

    // Gèrer la sélection : radio (si unique) ou checkbox (si multiple)
    function toggleOption(id, allowMultiple) {
        if (allowMultiple) {
            // Toggle : ajouter l’option si elle n’est pas encore sélectionnée, sinon la retirer
            const idx = selectedOptions.value.indexOf(id);
            if (idx === -1) selectedOptions.value.push(id);
            else selectedOptions.value.splice(idx, 1);
        } else {
            // Appliquer le choix simple
            selectedOptions.value = [id];
        }
    }

    return {
        poll, isOwner, hasVoted, expired,
        selectedOptions, loading, error, voteError,
        fetchPollByToken, submitVote, toggleOption,
    };
}
