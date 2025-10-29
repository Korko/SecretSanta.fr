<?php

namespace App\Jobs;

use App\Services\EmailClient;
use App\Services\MailTracker;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParseBounces implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(EmailClient $emailClient, MailTracker $tracker)
    {
        $unseenMails = $emailClient->getUnseenMails(500);

        foreach ($unseenMails as $unseenMail) {
            try {
                $deleteEmail = $tracker->isEmailReceived($unseenMail);

                $tracker->handle($unseenMail);

                if ($deleteEmail) {
                    $emailClient->delete($unseenMail);
                } else {
                    $emailClient->trash($unseenMail);
                }
            } catch(ModelNotFoundException) {
                // The email was already changed or something
                $emailClient->delete($unseenMail);
            } catch(Exception) {
                // Ignore that error
            }
        }
    }
}
