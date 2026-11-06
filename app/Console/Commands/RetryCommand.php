<?php

namespace App\Console\Commands;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Queue\Console\RetryCommand as BaseRetryCommand;

final class RetryCommand extends BaseRetryCommand
{
    /**
     * Retry the queue job.
     */
    protected function retryJob($job): void
    {
        try {
            parent::retryJob($job);
        } catch (ModelNotFoundException) {
            $this->laravel->make('queue.failer')->forget($job->id);
        }
    }
}
