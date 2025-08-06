<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Job for sending emails
 */
class EmailJob extends Model
{
    use HasFactory;

    // This model uses Laravel's `jobs` table
    protected $table = 'jobs';

    /**
     * Create an email sending job
     */
    public static function createEmailJob(string $type, array $data): void
    {
        \App\Jobs\SendEmail::dispatch($type, $data)
            ->onQueue('emails');
    }
}
