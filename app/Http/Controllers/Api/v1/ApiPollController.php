<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePollRequest;
use App\Http\Requests\StorePollVoteRequest;
use App\Http\Requests\UpdatePollRequest;
use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiPollController extends Controller {


    // [GET /api/v1/polls] Liste des sondages du user connecté
    public function index(Request $request)
    {
        // Renvoit les sondages d'un utilisateur avec leurs options, triés par les plus récents
        return $request->user()
            ->polls()
            ->with('options')
            ->latest()
            ->get();
    }


    // [POST /api/v1/polls] Créer un sondage
    public function store(StorePollRequest $request)
    {

        // Récupérer les données validées
        $data = $request->validated();

        // Création de l'instance du sondage
        $poll = new Poll([
            'question'               => $data['question'],
            'color'                  => $data['color'] ?? null,
            'is_draft'               => $data['is_draft'] ?? true,
            'allow_multiple_choices' => $data['allow_multiple_choices'] ?? false,
            'allow_vote_change'      => $data['allow_vote_change'] ?? false,
            'results_public'         => $data['results_public'] ?? false,
            'duration'               => $data['duration'] ?? null,
        ]);

        // Liaison du sondage au user
        $poll->user()->associate($request->user());

        // Génération du token secret de 32 caractères
        $poll->secret_token = Str::random(32);

        // Si on lance le poll directement à sa création, on enregistre la date de début
        if (!$poll->is_draft) {
            $poll->started_at = now();
            if ($poll->duration) {
                $poll->ends_at = now()->addSeconds($poll->duration); // Calcul de la date de fin avec la durée
            }
        }

        $poll->save();

        // Créer les options en masse
        foreach ($data['options'] as $opt) {
            $poll->options()->create(['label' => $opt['label']]);
        }

        // Réponse : poll créé avec ses options
        return response()->json($poll->load('options'), 201);
    }


    // [GET /api/v1/polls/{poll}] Détail d'un sondage d'un utilisateur
    public function show(Request $request, Poll $poll)
    {
        // Seul le propriétaire peut y accéder
        if ($poll->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        return $poll->load(['options', 'votes']);
    }


    // [PATCH /api/v1/polls/{poll}] Modifier un sondage
    public function update(UpdatePollRequest $request, Poll $poll)
    {
        // Vérifier que le user est bien le propriétaire
        if ($poll->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $data = $request->validated();

        // Passer un brouillon en sondage actif (champ modifié, précédemment true, maintenant false -> Passer en ligne)
        $launching = isset($data['is_draft']) && $data['is_draft'] === false && $poll->is_draft;

        // On met à jour les champs du sondage, mais pas ses options, qui sont traitées séparément après.
        $poll->fill(array_diff_key($data, ['options' => null]));

        if ($launching) {
            $poll->started_at = now();
            // Calcul de la fin du sondage au lancement
            $duration = $data['duration'] ?? $poll->duration;
            $poll->ends_at = $duration ? now()->addSeconds($duration) : null;
        // Si on modifie la durée d'un sondage déjà actif, on recalcule son échéance
        } elseif (array_key_exists('duration', $data) && $poll->started_at) {
            $poll->ends_at = $poll->duration
                ? $poll->started_at->copy()->addSeconds($poll->duration)
                : null;
        }

        $poll->save();

        // Mise à jour des options
        if (isset($data['options'])) {

            // Tableau pour stocker les options à conserver
            $keptOptionIds = [];

            // Mettre à jour les options existantes
            foreach ($data['options'] as $opt) {
                if (!empty($opt['id'])) {

                    // Rechercher si l'option existe
                    $option = $poll->options()->whereKey($opt['id'])->first();

                    // Si oui on la met à jour et on la stock dans le tableau
                    if ($option) {
                        $option->update(['label' => $opt['label']]);
                        $keptOptionIds[] = $option->id;
                        continue;
                    }
                }

                // Si l’option n’existait pas encore, on la crée puis on la stock dans le tableau
                $option = $poll->options()->create(['label' => $opt['label']]);
                $keptOptionIds[] = $option->id;
            }

            //Supprime les options non-conservées
            $poll->options()
                ->whereNotIn('id', $keptOptionIds)
                ->delete();
        }

        return $poll->load('options');
    }


    // [DELETE /api/v1/polls/{poll}] Supprimer un sondage
    public function destroy(Request $request, Poll $poll)
    {

        // Un utilisateur ne peut supprimer que ses propres polls
        if ($poll->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        // Suppression du poll (options + votes supprimés via cascade)
        $poll->delete();

        // Réponse : 204 No Content
        return response()->noContent();
    }


    // [GET /api/v1/polls/token/{token}] Affichage d'un sondage via son lien partage
    public function showByToken(Request $request, string $token)
    {

        // Chercher un sondage par son token, en chargeant directement ses options
        $poll = Poll::with('options')->where('secret_token', $token)->first();

        // Si aucun résultat
        if (!$poll) {
            return response()->json(['message' => 'Sondage introuvable.'], 404);
        }

        // Si le sondage est en brouillon
        if ($poll->is_draft) {
            return response()->json(['message' => 'Ce sondage n\'est pas encore disponible.'], 403);
        }

        // Vérifie si l'utilisateur connecté est le propriétaire
        $isOwner = $request->user()?->id === $poll->user_id;

        // Vérifie si l'utilisateur a déjà voté
        $hasVoted = $request->user()
            ? PollVote::where('poll_id', $poll->id)
                ->where('user_id', $request->user()->id)
                ->exists()
            : false;

        // Stockage des options déjà choisies par l’utilisateur pour ce sondage
        $userVoteOptionIds = $request->user()
            ? PollVote::where('poll_id', $poll->id)
                ->where('user_id', $request->user()->id)
                ->pluck('poll_option_id') // Extraire uniquement l'id
                ->values()
            : [];

        // Réponse : Affichage complet du sondage et des infos liées à l’utilisateur
        return response()->json([
            'poll'              => $poll,
            'isOwner'           => $isOwner,
            'hasVoted'          => $hasVoted,
            'userVoteOptionIds' => $userVoteOptionIds,
            'expired'           => $poll->isExpired(),
        ]);
    }


    // [POST /api/v1/polls/token/{token}/vote] Soumettre un vote
    public function vote(StorePollVoteRequest $request, string $token)
    {

        // Chercher un sondage par son token, en chargeant directement ses options
        $poll = Poll::with('options')->where('secret_token', $token)->first();

        // Si aucun résultat
        if (!$poll) {
            return response()->json(['message' => 'Sondage introuvable.'], 404);
        }

        // Un sondage en brouillon ne peut pas recevoir de vote
        if ($poll->is_draft) {
            return response()->json(['message' => 'Ce sondage n\'est pas encore ouvert au vote.'], 403);
        }

        // Un sondage expiré ne peut pas recevoir de vote
        if ($poll->isExpired()) {
            return response()->json(['message' => 'Ce sondage est terminé.'], 403);
        }

        // Récupérer l'id de l'utilisateur et des options
        $userId = $request->user()->id;
        $optionIds = $request->validated()['option_ids'];

        // Vérifier que les options appartiennent bien à ce sondage
        $validIds = $poll->options->pluck('id')->toArray();
        foreach ($optionIds as $id) {
            if (!in_array($id, $validIds)) {
                return response()->json(['message' => 'Option invalide.'], 422);
            }
        }

        // Chercher si l’utilisateur a déjà voté pour ce sondage
        $existingVote = PollVote::where('poll_id', $poll->id)
            ->where('user_id', $userId)
            ->first();

        // Erreur choix multiple non-autorisé
        if (!$poll->allow_multiple_choices && count($optionIds) > 1) {
            return response()->json(['message' => 'Ce sondage n\'accepte qu\'un seul choix.'], 422);
        }

        // Autoriser la modification du vote si le paramètre du sondage le permet
        if ($existingVote) {
            if (!$poll->allow_vote_change) {
                return response()->json(['message' => 'Vous avez déjà voté.'], 403);
            }
            // Supprimer l'ancien vote pour le remplacer
            PollVote::where('poll_id', $poll->id)->where('user_id', $userId)->delete();
        }

        // Enregistrer le ou les votes
        foreach ($optionIds as $optionId) {
            PollVote::create([
                'poll_id'        => $poll->id,
                'user_id'        => $userId,
                'poll_option_id' => $optionId,
            ]);
        }

        return response()->json(['message' => 'Vote enregistré.'], 201);
    }


    // [GET /api/v1/polls/token/{token}/results] Affichage des résultats d'un sondage
    public function results(Request $request, string $token)
    {
        // Charger le sondage lié au token, avec ses options et le nombre de votes par option
        $poll = Poll::with(['options' => function ($q) {
            $q->withCount('votes');
        }])->where('secret_token', $token)->first();

        // Si le sondage n'existe pas
        if (!$poll) {
            return response()->json(['message' => 'Sondage introuvable.'], 404);
        }

        // Vérifier que l'utilisateur possède le sondage
        $isOwner = $request->user()?->id === $poll->user_id;

        // Refuser l'acces si les resultats sont prives et que l'utilisateur n'est pas le proprietaire
        if (!$poll->results_public && !$isOwner) {
            return response()->json(['message' => 'Résultats non publics.'], 403);
        }

        // Calculer le nombre de votes
        $totalVotes = $poll->options->sum('votes_count');

        // Ajouter le pourcentage calculé à chaque option
        $options = $poll->options->map(function ($option) use ($totalVotes) {
            return [
                'id'          => $option->id,
                'label'       => $option->label,
                'votes_count' => $option->votes_count,
                'percentage'  => $totalVotes > 0
                    ? round(($option->votes_count / $totalVotes) * 100)
                    : 0,
            ];
        });

        // Réponse complète
        return response()->json([
            'poll'        => $poll->only(['id', 'question', 'is_draft', 'ends_at', 'results_public']),
            'options'     => $options,
            'total_votes' => $totalVotes,
            'expired'     => $poll->isExpired(),
        ]);
    }
}
