<?php

namespace App\Jobs;

use App\Models\Mail as MailModel;
use App\Services\EmailClient;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webklex\IMAP\Message as EmailMessage;

class ParseBounces implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

                $mail = $this->getMailFromReturnPath($recipient);
                $mail->delivery_status = MailModel::ERROR;
                $mail->save();
            } catch (Exception $e) {
                // Just ignore the exception
            } finally {
                $unseenMail->delete();
            }
        }
    }

    protected function getMailFromReturnPath(string $recipient): MailModel
    {
        $params = sscanf($recipient, str_replace('*', '%[0-9a-zA-Z]', config('mail.return_path')));

        return MailModel::findByHashOrFail(collect((array) $params)->first());
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
