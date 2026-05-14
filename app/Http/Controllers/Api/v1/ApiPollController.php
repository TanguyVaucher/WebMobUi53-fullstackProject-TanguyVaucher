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

class ApiPollController extends Controller
{
    // GET /api/v1/polls — liste des sondages du user connecté
    public function index(Request $request)
    {
        return $request->user()
            ->polls()
            ->with('options')
            ->latest()
            ->get();
    }

    // POST /api/v1/polls — créer un sondage
    public function store(StorePollRequest $request)
    {
        $data = $request->validated();

        $poll = new Poll([
            'question'               => $data['question'],
            'color'                  => $data['color'] ?? null,
            'is_draft'               => $data['is_draft'] ?? true,
            'allow_multiple_choices' => $data['allow_multiple_choices'] ?? false,
            'allow_vote_change'      => $data['allow_vote_change'] ?? false,
            'results_public'         => $data['results_public'] ?? false,
            'duration'               => $data['duration'] ?? null,
        ]);

        $poll->user()->associate($request->user());
        $poll->secret_token = Str::random(32);

        // Si on lance directement à la création, on enregistre la date de début
        if (!$poll->is_draft) {
            $poll->started_at = now();
            if ($poll->duration) {
                $poll->ends_at = now()->addSeconds($poll->duration);
            }
        }

        $poll->save();

        // Créer les options en masse
        foreach ($data['options'] as $opt) {
            $poll->options()->create(['label' => $opt['label']]);
        }

        return response()->json($poll->load('options'), 201);
    }

    // GET /api/v1/polls/{poll} — détail (propriétaire uniquement)
    public function show(Request $request, Poll $poll)
    {
        // Seul le propriétaire peut voir le détail complet
        if ($poll->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        return $poll->load(['options', 'votes']);
    }

    // PATCH /api/v1/polls/{poll} — modifier un sondage
    public function update(UpdatePollRequest $request, Poll $poll)
    {
        // Vérifier que le user est bien le propriétaire
        if ($poll->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $data = $request->validated();

        // Détecter le lancement du sondage (brouillon → actif)
        $launching = isset($data['is_draft']) && $data['is_draft'] === false && $poll->is_draft;

        $poll->fill(array_diff_key($data, ['options' => null]));

        if ($launching) {
            $poll->started_at = now();
            // Calculer ends_at si une durée est définie
            $duration = $data['duration'] ?? $poll->duration;
            $poll->ends_at = $duration ? now()->addSeconds($duration) : null;
        } elseif (array_key_exists('duration', $data) && $poll->started_at) {
            $poll->ends_at = $poll->duration
                ? $poll->started_at->copy()->addSeconds($poll->duration)
                : null;
        }

        $poll->save();

        // Synchroniser les options si elles sont envoyées
        if (isset($data['options'])) {
            $keptOptionIds = [];

            foreach ($data['options'] as $opt) {
                if (!empty($opt['id'])) {
                    $option = $poll->options()->whereKey($opt['id'])->first();

                    if ($option) {
                        $option->update(['label' => $opt['label']]);
                        $keptOptionIds[] = $option->id;
                        continue;
                    }
                }

                $option = $poll->options()->create(['label' => $opt['label']]);
                $keptOptionIds[] = $option->id;
            }

            $poll->options()
                ->whereNotIn('id', $keptOptionIds)
                ->delete();
        }

        return $poll->load('options');
    }

    // DELETE /api/v1/polls/{poll} — supprimer un sondage
    public function destroy(Request $request, Poll $poll)
    {
        if ($poll->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        // La cascade est gérée par les FK en base (options + votes supprimés)
        $poll->delete();

        return response()->noContent();
    }

    // GET /api/v1/polls/token/{token} — affichage public via lien de partage
    public function showByToken(Request $request, string $token)
    {
        $poll = Poll::with('options')->where('secret_token', $token)->first();

        if (!$poll) {
            return response()->json(['message' => 'Sondage introuvable.'], 404);
        }

        if ($poll->is_draft) {
            return response()->json(['message' => 'Ce sondage n\'est pas encore disponible.'], 403);
        }

        // Indique si l'utilisateur connecté est le propriétaire
        $isOwner = $request->user()?->id === $poll->user_id;

        // Indique si l'utilisateur a déjà voté
        $hasVoted = $request->user()
            ? PollVote::where('poll_id', $poll->id)
                ->where('user_id', $request->user()->id)
                ->exists()
            : false;

        $userVoteOptionIds = $request->user()
            ? PollVote::where('poll_id', $poll->id)
                ->where('user_id', $request->user()->id)
                ->pluck('poll_option_id')
                ->values()
            : [];

        return response()->json([
            'poll'              => $poll,
            'isOwner'           => $isOwner,
            'hasVoted'          => $hasVoted,
            'userVoteOptionIds' => $userVoteOptionIds,
            'expired'           => $poll->isExpired(),
        ]);
    }

    // POST /api/v1/polls/token/{token}/vote — soumettre un vote
    public function vote(StorePollVoteRequest $request, string $token)
    {
        $poll = Poll::with('options')->where('secret_token', $token)->first();

        if (!$poll) {
            return response()->json(['message' => 'Sondage introuvable.'], 404);
        }

        // Un sondage en brouillon ne reçoit pas de vote
        if ($poll->is_draft) {
            return response()->json(['message' => 'Ce sondage n\'est pas encore ouvert au vote.'], 403);
        }

        // Un sondage expiré ne reçoit plus de vote
        if ($poll->isExpired()) {
            return response()->json(['message' => 'Ce sondage est terminé.'], 403);
        }

        $userId = $request->user()->id;
        $optionIds = $request->validated()['option_ids'];

        // Vérifier que les options appartiennent bien à ce sondage
        $validIds = $poll->options->pluck('id')->toArray();
        foreach ($optionIds as $id) {
            if (!in_array($id, $validIds)) {
                return response()->json(['message' => 'Option invalide.'], 422);
            }
        }

        // Pour les sondages à choix unique : un seul vote autorisé
        $existingVote = PollVote::where('poll_id', $poll->id)
            ->where('user_id', $userId)
            ->first();

        if (!$poll->allow_multiple_choices && count($optionIds) > 1) {
            return response()->json(['message' => 'Ce sondage n\'accepte qu\'un seul choix.'], 422);
        }

        if ($existingVote) {
            // Autoriser la modification si le sondage le permet
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

    // GET /api/v1/polls/token/{token}/results — résultats avec comptage
    public function results(Request $request, string $token)
    {
        $poll = Poll::with(['options' => function ($q) {
            $q->withCount('votes');
        }])->where('secret_token', $token)->first();

        if (!$poll) {
            return response()->json(['message' => 'Sondage introuvable.'], 404);
        }

        $isOwner = $request->user()?->id === $poll->user_id;

        // Les résultats sont visibles si : publics, ou si on est le propriétaire
        if (!$poll->results_public && !$isOwner) {
            return response()->json(['message' => 'Résultats non publics.'], 403);
        }

        $totalVotes = $poll->options->sum('votes_count');

        // Ajouter le pourcentage à chaque option
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

        return response()->json([
            'poll'        => $poll->only(['id', 'question', 'is_draft', 'ends_at', 'results_public']),
            'options'     => $options,
            'total_votes' => $totalVotes,
            'expired'     => $poll->isExpired(),
        ]);
    }
}
