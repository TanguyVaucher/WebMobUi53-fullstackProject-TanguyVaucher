<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    // Champs qu'on peut remplir en masse (create/update)
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

    // Conversions automatiques des types
    protected $casts = [
        'is_draft'               => 'boolean',
        'allow_multiple_choices' => 'boolean',
        'allow_vote_change'      => 'boolean',
        'results_public'         => 'boolean',
        'started_at'             => 'datetime',
        'ends_at'                => 'datetime',
    ];

    // Vérifie si le sondage est terminé (ends_at dans le passé)
    public function isExpired(): bool
    {
        return $this->ends_at !== null && $this->ends_at->isPast();
    }

    // Vérifie si le sondage est actif (lancé + pas expiré)
    public function isActive(): bool
    {
        return !$this->is_draft && !$this->isExpired();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }
}
