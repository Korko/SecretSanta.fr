<?php

namespace Tests;

use App\Jobs\SendMail;
use Closure;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Mail\Mailable;
use Mail;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function assertHasMailPushed($class, $recipient = null, Closure $callback = null)
    {
        Mail::assertSent(function (Mailable $mail) use ($class, $recipient, $callback) {
            if (
                $mail instanceof $class &&
                ($recipient === null || $mail->hasTo($recipient))
            ) {
                if ($callback !== null) {
                    $callback($mail);
                }

                return true;
            }

            return false;
        });
    }
}
