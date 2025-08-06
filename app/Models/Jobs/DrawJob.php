<?php

namespace App\Models\Jobs;

use App\Models\Draw\Draw;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Job pour effectuer le tirage au sort
 */
class DrawJob extends Model
{
    use HasFactory;

    // Ce modèle utilise la table `jobs` de Laravel
    protected $table = 'jobs';

    /**
     * Crée un job de tirage
     */
    public static function createDrawJob(Draw $draw, array $parameters = []): void
    {
        \App\Jobs\ProcessDraw::dispatch($draw, $parameters)
            ->onQueue('draws');
    }
}
