<?php

namespace App\Console\Commands;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Queue\Console\RetryCommand as BaseRetryCommand;

class RetryCommand extends BaseRetryCommand
{
    /**
     * Retry the queue job.
     */
    protected function retryJob(stdClass $job): void
    {
        try {
            parent::retryJob($job);
        } catch (ModelNotFoundException $e) {
            $this->laravel['queue.failer']->forget($job->id);
        }
    }
}
