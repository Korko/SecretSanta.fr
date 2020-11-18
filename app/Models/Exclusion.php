<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exclusion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['participant_id', 'exclusion_id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
