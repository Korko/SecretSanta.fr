<?php

namespace App\Mail;

use Facades\App\Services\MailTracker;
use Illuminate\Mail\Mailable;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;

abstract class TrackedMailable extends Mailable
{
    protected $mailable;

    /**
     * Send the message using the given mailer.
     *
     * @param  \Illuminate\Contracts\Mail\Factory|\Illuminate\Contracts\Mail\Mailer  $mailer
     * @return void
     */
    public function send($mailer)
    {
        $this->mailable = $this->getMailable();

        $this->withSymfonyMessage(function (Email $message) {
            // In case of Bounce
            $message->getHeaders()->addPathHeader('Return-Path', MailTracker::getBounceReturnPath($this->mailable->mail));

            // To assert Reception
            $message->getHeaders()->addPathHeader('X-Confirm-Reading-To', MailTracker::getConfirmReturnPath($this->mailable->mail));
            $message->getHeaders()->addPathHeader('Return-Receipt-To', MailTracker::getConfirmReturnPath($this->mailable->mail));
            $message->getHeaders()->addPathHeader('Disposition-Notification-To', MailTracker::getConfirmReturnPath($this->mailable->mail));
        });

        try {
            $this->mailable->mail->markAsSending();

            parent::send($mailer);

            $this->mailable->mail->markAsSent();
        } catch (TransportExceptionInterface $exception) {
            $this->mailable->mail->markAsError();
        }
    }

    /**
     * Return a mailable (morpheable into \App\Models\Mail) instance
     */
    abstract protected function getMailable();
}
