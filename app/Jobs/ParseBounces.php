<?php

namespace App\Jobs;

use App\Services\EmailClient;
use App\Services\MailTracker;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParseBounces implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(EmailClient $emailClient, MailTracker $tracker)
    {
        $unseenMails = $emailClient->getUnseenMails();

        foreach ($unseenMails as $unseenMail) {
            try {
                $tracker->handle($unseenMail);

                $unseenMail->delete();
            } catch(Exception) {
                // Ignore that error
            }
        }
    }
}
