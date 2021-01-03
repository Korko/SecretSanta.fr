<?php

namespace App\Jobs;

use App\Mail\TrackedMailable;
use App\Models\Mail as MailModel;
use App\Services\EmailClient;
use App\Traits\UpdatesMailDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webklex\PHPIMAP\Exceptions\MessageHeaderFetchingException;
use Webklex\PHPIMAP\Message as EmailMessage;

class ParseBounces implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, UpdatesMailDelivery;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(EmailClient $emailClient)
    {
        $unseenMails = $emailClient->getUnseenMails();
        foreach ($unseenMails as $unseenMail) {
            $this->handleMail($unseenMail);

            try {
                $unseenMail->move(config('imap.folders.trash'));
            } catch(MessageHeaderFetchingException $e) {
                // Ignore that error
            }
        }
    }

    protected function handleMail(EmailMessage $message): void
    {
        $recipient = $this->getFirstRecipientAddress($message);

        $params = (array) sscanf(
            $recipient,
            str_replace('*', '%[a-z]-%[0-9a-zA-Z]-%d', config('mail.return_path'))
        );

        if(!empty($params[0])) {
            $mail = MailModel::findByHashOrFail($params[1]);

            switch($params[0]) {
                case TrackedMailable::BOUNCE:
                    // TODO: Determine depending on the bounce error, if the failure is temporary (4xx) or final (5xx)
                    // Auto-retry or not
                    $this->updateDelivery($mail, MailModel::ERROR, $params[2]);
                    break;
                case TrackedMailable::CONFIRM:
                    $this->updateDelivery($mail, MailModel::RECEIVED, $params[2]);
                    break;
            }
        }
    }

    protected function getFirstRecipientAddress(EmailMessage $message): string
    {
        $recipient = collect($message->getTo())
            ->first();

        return is_object($recipient) ? $recipient->mailbox : '';
    }

    protected function parseBounce($recipient): ?array
    {


        return $params[0] ? $params : null;
    }
}
