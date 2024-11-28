<?php

namespace App\Channels;

use App\Models\Mail as MailModel;
use Closure;
use Facades\App\Services\MailTracker;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Mail;
use Swift_TransportException;
use URL;

class TrackedMailChannel extends MailChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $mail = $notification->getMailableModel($notifiable)->mail;

        try {
            $mail->markAsSending();

            parent::send($notifiable, $notification);

            $mail->markAsSent();
        } catch (Swift_TransportException $exception) {
            $mail->markAsError();
        }
    }

    /**
     * Get the mailer Closure for the message.
     *
     * @param  mixed  $notifiable
     * @param Notification $notification
     * @param  MailMessage  $message
     * @return Closure
     */
    protected function messageBuilder($notifiable, $notification, $message)
    {
        $mail = $notification->getMailableModel($notifiable)->mail;

        $message->withSwiftMessage(function ($message) use ($mail) {
            // In case of Bounce
            $message->getHeaders()->addPathHeader('Return-Path', MailTracker::getBounceReturnPath($mail));

            // To assert Reception
            $message->getHeaders()->addPathHeader('X-Confirm-Reading-To', MailTracker::getConfirmReturnPath($mail));
            $message->getHeaders()->addPathHeader('Return-Receipt-To', MailTracker::getConfirmReturnPath($mail));
            $message->getHeaders()->addPathHeader('Disposition-Notification-To', MailTracker::getConfirmReturnPath($mail));
        });

        return parent::messageBuilder($notifiable, $notification, $message);
    }
}
