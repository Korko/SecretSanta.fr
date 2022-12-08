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
        $recipients = $this->getRecipients($emailClient, 500);

        foreach($recipients as $recipient) {
            $recipients->send($tracker->handle($recipient));
        }
    }

    protected function getRecipients(EmailClient $emailClient, $limit = 100)
    {
        $unseenMails = $emailClient->getUnseenMails($limit);
        foreach ($unseenMails as $unseenMail) {
            $success = (yield $this->getFirstRecipientAddress($unseenMail)) ?? true;

            if($success) {
                try {
                    $unseenMail->delete();
                } catch(MessageHeaderFetchingException $e) {
                    // Ignore that error
                    debug($e);
                }
            }
        }
    }

    protected function getFirstRecipientAddress(EmailMessage $message): string
    {
        $recipient = $message->getTo()[0];

        return is_object($recipient) ? $recipient->mailbox : '';
    }
}
