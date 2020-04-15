<?php

namespace App\Jobs;

use App\Mail as MailModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webklex\IMAP\Facades\Client as EmailClient;
use Webklex\IMAP\Message as EmailMessage;

class ParseBounces implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $unseenMails = $this->getUnseenMails();
        foreach ($unseenMails as $unseenMail) {
            $to = $this->getFirstRecipientAddress($unseenMail);

            if (! empty($to)) {
                [$hash] = sscanf($to, str_replace('*', '%s', config('mail.return_path')));

                if ($hash !== null) {
                    try {
                        $mail = MailModel::findByHashOrFail($hash);
                        $mail->delivery_status = MailModel::ERROR;
                        $mail->save();
                    } catch (Exception $e) {
                        //
                    } finally {
                        $unseenMail->delete();
                }
            }
        }
    }

    protected function getUnseenMails(): Iterable
    {
        // Connect to the IMAP/POP folder
        $oClient = EmailClient::account('default');
        $oClient->connect();
        $oFolder = $oClient->getFolder('INBOX');

        return $oFolder->query()->unseen()->get();
    }

    protected function getFirstRecipientAddress(EmailMessage $message)
    {
        $recipient = collect($message->getTo())
            ->first();

        return $recipient !== null ?
            $recipient->mailbox :
            null;
    }
}
