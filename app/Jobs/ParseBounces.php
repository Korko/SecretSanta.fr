<?php

namespace App\Jobs;

use App\Mail\TrackedMailable;
use App\Services\MailTracker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Contracts\Mailbox;
use App\Mailbox\EmailMessage;
use Webklex\PHPIMAP\Exceptions\MessageHeaderFetchingException;

class ParseBounces implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(Mailbox $mailbox, MailTracker $tracker)
    {
        $recipients = $this->getRecipients($mailbox);

        foreach($recipients as $recipient) {
            $tracker->handle($recipient);
        }
    }

    protected function getRecipients(Mailbox $mailbox)
    {
        $unseenMails = $mailbox->getUnseenMails();
        foreach ($unseenMails as $unseenMail) {
            yield $this->getFirstRecipientAddress($unseenMail);

            try {
                $unseenMail->move(config('imap.folders.trash'));
            } catch(MessageHeaderFetchingException $e) {
                // Ignore that error
            }
        }
    }

    protected function getFirstRecipientAddress(EmailMessage $message): string
    {
        $recipient = $message->getTo()[0];

        return is_object($recipient) ? $recipient->mailbox : '';
    }
}
