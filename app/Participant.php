<?php

namespace Korko\SecretSanta;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    const CHALLENGE = 'Ping?';

    public $timestamps = false;
}
