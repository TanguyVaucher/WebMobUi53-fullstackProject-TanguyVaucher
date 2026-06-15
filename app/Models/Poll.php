<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    // Champs constituant un poll et autorisés à être remplis en une seule opération (create/update)
    protected $fillable = [
        'title',
        'question',
        'color',
        'is_draft',
        'allow_multiple_choices',
        'allow_vote_change',
        'results_public',
        'duration',
        'started_at',
        'ends_at',
    ];

    // Cast des types
    protected $casts = [
        'is_draft'               => 'boolean', /* DB : 0/1 <-> PHP true/false */
        'allow_multiple_choices' => 'boolean',
        'allow_vote_change'      => 'boolean',
        'results_public'         => 'boolean',
        'started_at'             => 'datetime', /* DB : date/heure <-> PHP objet date */
        'ends_at'                => 'datetime',
    ];

    // Vérification si un sondage est terminé (ends_at dans le passé)
    public function isExpired(): bool
    {
        return $this->ends_at !== null && $this->ends_at->isPast();
    }

    // Vérification si un sondage est actif (publié et non-expiré)
    public function isActive(): bool
    {
        return !$this->is_draft && !$this->isExpired();
    }

    // Relations Eloquent

    // Un sondage appartient à un utilisateur
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Un sondage possède 2..* option(s)
    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    // Un sondage possède 0..* vote(s)
    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }
}
