import { ref } from 'vue';
import { useFetchApi } from './useFetchApi';

/**
 * Gère l'affichage et le vote d'un sondage via son token.
 * Utilisé dans PollVote.vue.
 */
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

    // Charge le sondage public via son token
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

    // Soumet le vote, retourne true si succès
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

    // Gère la sélection : radio (choix unique) ou checkbox (choix multiple)
    function toggleOption(id, allowMultiple) {
        if (allowMultiple) {
            // Toggle dans le tableau
            const idx = selectedOptions.value.indexOf(id);
            if (idx === -1) selectedOptions.value.push(id);
            else selectedOptions.value.splice(idx, 1);
        } else {
            // Remplacer par le seul choix
            selectedOptions.value = [id];
        }
    }

    return {
        poll, isOwner, hasVoted, expired,
        selectedOptions, loading, error, voteError,
        fetchPollByToken, submitVote, toggleOption,
    };
}
