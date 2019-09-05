<?php

namespace App;

use App\Database\Model;
use App\Services\SymmetricalEncrypter;

class DearSanta extends Model
{
    public $timestamps = false;

    protected $encryptable = [
        'santa_name',
        'santa_email',
        'challenge',
    ];

    public function save(array $options = [])
    {
        $this->challenge = config('app.challenge');

        return parent::save($options);
    }

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }
}
