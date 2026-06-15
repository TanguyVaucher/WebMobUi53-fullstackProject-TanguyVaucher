<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollVote extends Model
{
    // Champs autorisés
    protected $fillable = ['poll_id', 'user_id', 'poll_option_id'];

    // Relations Eloquent

    // Un vote est lié à un sondage
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    // Un vote est lié à un utilisateur
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Un vote est lié à une option de sondage
    public function option(): BelongsTo
    {
        return $this->belongsTo(PollOption::class, 'poll_option_id');
    }
}
