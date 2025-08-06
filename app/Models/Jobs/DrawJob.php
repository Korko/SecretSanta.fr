<?php

namespace App\Models\Jobs;

use App\Models\Draw\Draw;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Job to perform the draw
 */
class DrawJob extends Model
{
    use HasFactory;

    // This model uses Laravel's `jobs` table
    protected $table = 'jobs';

    /**
     * Create a draw job
     */
    public static function createDrawJob(Draw $draw, array $parameters = []): void
    {
        \App\Jobs\ProcessDrawJob::dispatch($draw, $parameters)
            ->onQueue('draws');
    }
}
