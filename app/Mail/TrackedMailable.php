<?php

namespace App\Mail;

use App\Events\MailStatusUpdated;
use App\Jobs\UpdateMailDelivery;
use App\Models\Mail as MailModel;
use App\Traits\UpdatesMailDelivery;
use Illuminate\Foundation\Bus\DispatchesJobs;

class TrackedMailable extends Mailable
{
    use DispatchesJobs, UpdatesMailDelivery;

    public $returnPath;

    protected function track(MailModel $mail)
    {
        $this->store($mail);

        $this->returnPath = str_replace('*', $mail->hash.'-'.$mail->version, config('mail.return_path'));
    }

    public function failed()
    {
        $this->delayedUpdateDelivery(MailModel::ERROR);
    }

    /**
     * Define the return-path in case of bounce
     * and send the mail
     *
     * @param  \Illuminate\Contracts\Mail\Factory|\Illuminate\Contracts\Mail\Mailer  $mailer
     * @return void
     */
    public function send($mailer): void
    {
        $mail = $this->delayedUpdateDelivery(MailModel::SENDING);

        dispatch((new UpdateMailDelivery($mail, MailModel::SENT))->delay(120));

        $this->withSwiftMessage(function ($message) use ($mail) {
            $message->getHeaders()->addPathHeader('Return-Path', $this->returnPath);
            $message->getHeaders()->addDateHeader('X-Updated-At', $mail->updated_at);
        });

        parent::send($mailer);
    }
}