<?php

namespace App\Jobs;

use App\Models\Mail as MailModel;
use App\Services\EmailClient;
use App\Traits\UpdatesMailDelivery;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
            try {
                $recipient = $this->getFirstRecipientAddress($unseenMail);

                // TODO: Determine depending on the bounce error, if the failure is temporary (4xx) or final (5xx)
                // Auto-retry or not
                $params = collect((array) sscanf(
                    $recipient,
                    str_replace('*', '%[0-9a-zA-Z]-%d', config('mail.return_path'))
                ));

                $mail = MailModel::findByHashOrFail($params[0]);

                $this->updateDelivery($mail, MailModel::ERROR, $params[1]);
            } catch (Exception $e) {
                // Just ignore the exception
            } finally {
                $unseenMail->move(config('imap.folders.trash'));
            }
        }
    }

    protected function getFirstRecipientAddress(EmailMessage $message): string
    {
        $recipient = collect($message->getTo())
            ->first();

        if (! is_object($recipient)) {
            throw new Exception('No recipient available');
        }

        return $recipient->mailbox;
    }
}
