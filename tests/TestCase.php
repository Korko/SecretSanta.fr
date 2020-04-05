<?php

namespace Tests;

use App\Jobs\SendMail;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Queue;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function assertQueueHasMailPushed($class, $recipient = null, $callback = null)
    {
        Queue::assertPushed(SendMail::class, function ($job) use ($class, $recipient, $callback) {
            if (
                $job->getMailable() instanceof $class &&
                ($recipient === null || $job->getRecipient()->email === $recipient)
            ) {
                if ($callback !== null) {
                    $callback($job->getMailable());
                }

                return true;
            }

            return false;
        });
    }
}
