<?php

namespace App\Jobs;

use App\Contracts\EmailMessage;
use App\Contracts\Mailbox;
use App\Services\MailTracker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParseBounces implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(Mailbox $mailbox, MailTracker $tracker)
    {
        $recipients = $this->getRecipients($mailbox);

        foreach ($recipients as $recipient) {
            $tracker->handle($recipient);
        }
    }

    protected function getRecipients(Mailbox $mailbox)
    {
        $unseenMails = $mailbox->getUnseenMails();
        foreach ($unseenMails as $index => &$unseenMail) {
            yield $this->getFirstRecipientAddress($unseenMail);

            unset($unseenMails[$index]);
        }
        unset($unseenMail);
    }

    protected function getFirstRecipientAddress(EmailMessage $message): string
    {
        return $message->getTo()[0];
    }
}
