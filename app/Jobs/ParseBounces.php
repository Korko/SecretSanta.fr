<?php

namespace App\Jobs;

use App\Services\EmailClient;
use App\Services\MailTracker;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Queue\Queueable;

final class ParseBounces implements ShouldQueue
{
    use Queueable;

    public function handle(EmailClient $emailClient, MailTracker $tracker): void
    {
        $unseenMails = $emailClient->getUnseenMails(1000);

        foreach ($unseenMails as $unseenMail) {
            try {
                $deleteEmail = $tracker->isEmailReceived($unseenMail);

                $tracker->handle($unseenMail);

                if ($deleteEmail) {
                    $emailClient->delete($unseenMail);
                } else {
                    $emailClient->trash($unseenMail);
                }
            } catch (ModelNotFoundException) {
                // The email was already changed or something
                $emailClient->delete($unseenMail);
            } catch (Exception) {
                // Ignore that error
            }
        }
    }
}
