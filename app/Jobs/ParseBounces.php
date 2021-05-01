<?php

namespace App\Jobs;

use App\Mail\TrackedMailable;
use App\Models\Mail as MailModel;
use App\Services\EmailClient;
use App\Services\MailTracker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webklex\PHPIMAP\Exceptions\MessageHeaderFetchingException;
use Webklex\PHPIMAP\Message as EmailMessage;

class ParseBounces implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(EmailClient $emailClient, MailTracker $tracker)
    {
        $recipients = $this->getRecipients($emailClient);

        foreach($recipients as $recipient) {
            $tracker->handle($this->getReturnPath($recipient));
        }
    }

    protected function getRecipients(EmailClient $emailClient)
    {
        $unseenMails = $emailClient->getUnseenMails();
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
        $recipient = collect($message->getTo())
            ->first();

        return is_object($recipient) ? $recipient->mailbox : '';
    }

    protected function getReturnPath($email): string
    {
        return strstr($email, '@', true);
    }
}
