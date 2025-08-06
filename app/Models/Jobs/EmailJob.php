<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Job pour l'envoi d'emails
 */
class EmailJob extends Model
{
    use HasFactory;

    // Ce modèle utilise la table `jobs` de Laravel
    protected $table = 'jobs';

    /**
     * Crée un job d'envoi d'email
     */
    public static function createEmailJob(string $type, array $data): void
    {
        \App\Jobs\SendEmail::dispatch($type, $data)
            ->onQueue('emails');
    }
}
