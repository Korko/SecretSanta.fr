<?php

namespace App\Mail;

use App\Events\MailStatusUpdated;
use App\Jobs\UpdateMailDelivery;
use App\Models\Mail as MailModel;
use App\Traits\UpdatesMailDelivery;
use Illuminate\Foundation\Bus\DispatchesJobs;
use URL;

class TrackedMailable extends Mailable
{
    use DispatchesJobs, UpdatesMailDelivery;

    const BOUNCE = 'bounce';
    const CONFIRM = 'confirm';

    public $bounceReturnPath;
    public $confirmReturnPath;
    public $trackedPixel;

    protected function track(MailModel $mail)
    {
        $this->store($mail);

        $this->bounceReturnPath = str_replace('*', self::BOUNCE.'-'.$mail->hash.'-'.$mail->version, config('mail.return_path'));
        $this->confirmReturnPath = str_replace('*', self::CONFIRM.'-'.$mail->hash.'-'.$mail->version, config('mail.return_path'));
        $this->trackedPixel = URL::signedRoute('pixel', [
            'mail' => $mail->hash,
            'version' => $mail->version,
        ]);
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

        dispatch((new UpdateMailDelivery($mail, MailModel::SENT))->delay(10));

        $this->withSwiftMessage(function ($message) use ($mail) {
            // In case of Bounce
            $message->getHeaders()->addPathHeader('Return-Path', $this->bounceReturnPath);

            // To assert Reception
            $message->getHeaders()->addPathHeader('X-Confirm-Reading-To', $this->confirmReturnPath);
            $message->getHeaders()->addPathHeader('Return-Receipt-To', $this->confirmReturnPath);
            $message->getHeaders()->addPathHeader('Disposition-Notification-To', $this->confirmReturnPath);
        });

        parent::send($mailer);
    }
}
