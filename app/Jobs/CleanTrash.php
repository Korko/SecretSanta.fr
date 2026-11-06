<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\EmailClient;
use App\Services\MailTracker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class CleanTrash implements ShouldQueue
{
    use Queueable;

    public function handle(EmailClient $emailClient, MailTracker $tracker): void
    {
        $oldMails = $emailClient->getOldMails(7, 500);

        foreach ($oldMails as $oldMail) {
            $emailClient->delete($oldMail);
        }
    }
}
