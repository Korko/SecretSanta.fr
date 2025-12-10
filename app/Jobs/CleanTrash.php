<?php

namespace App\Jobs;

use App\Services\EmailClient;
use App\Services\MailTracker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanTrash implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(EmailClient $emailClient, MailTracker $tracker)
    {
        $oldMails = $emailClient->getOldMails(7, 500);

        foreach ($oldMails as $oldMail) {
            $emailClient->delete($oldMail);
        }
    }
}
