<?php

namespace App\Console\Commands;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Queue\Console\RetryCommand as BaseRetryCommand;

class RetryCommand extends BaseRetryCommand
{
    /**
     * Retry the queue job.
     *
     * @param  \stdClass  $job
     * @return void
     */
    protected function retryJob($job)
    {
        try {
            parent::retryJob($job);
        } catch(ModelNotFoundException $e) {
            $this->laravel['queue.failer']->forget($job->id);
        }
    }
}
