<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ParticipantToken
 *
 * @property-read \App\Models\Participant $participant
 * @method static \Illuminate\Database\Eloquent\Builder|ParticipantToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ParticipantToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ParticipantToken query()
 * @mixin \Eloquent
 */
class ParticipantToken extends Model
{
    use HasFactory;

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
