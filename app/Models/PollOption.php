<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PollOption extends Model
{
    // Champs autorisés
    protected $fillable = ['poll_id', 'label'];

    // Relations Eloquent

    // Une option de sondage appartient à un sondage
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    // Une option de sondage possède 0..* vote(s)
    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }
}
