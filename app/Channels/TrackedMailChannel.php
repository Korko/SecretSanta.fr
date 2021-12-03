<?php

namespace App\Channels;

use App\Models\Mail as MailModel;
use Facades\App\Services\MailTracker;
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
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $mailable = $notification->getMailableModel($notifiable);
        if($mailable->mail) {
            $mail = $mailable->mail;
        } else {
            $mail = (new MailModel());
        }

        $mail->markAsSending();

        $mail->notification = $notification->id;
        $mailable->mail()->save($mail);

        try {
            parent::send($notifiable, $notification);

            $mail->markAsSent();
        } catch (Swift_TransportException $exception) {
            $mail->markAsError();
        }
    }

    /**
     * Get additional meta-data to pass along with the view data.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array
     */
    protected function additionalMessageData($notification)
    {
        return array_merge(parent::additionalMessageData($notification), [
            'trackedPixel' => URL::signedRoute('pixel', [
                'mail' => $notification->id,
            ]),
        ]);
    }

    /**
     * Get the mailer Closure for the message.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @param  \Illuminate\Notifications\Messages\MailMessage  $message
     * @return \Closure
     */
    protected function messageBuilder($notifiable, $notification, $message)
    {
        $message->withSwiftMessage(function ($message) use ($notification) {
            // In case of Bounce
            $message->getHeaders()->addPathHeader('Return-Path', MailTracker::getBounceReturnPath($notification));

            // To assert Reception
            $message->getHeaders()->addPathHeader('X-Confirm-Reading-To', MailTracker::getConfirmReturnPath($notification));
            $message->getHeaders()->addPathHeader('Return-Receipt-To', MailTracker::getConfirmReturnPath($notification));
            $message->getHeaders()->addPathHeader('Disposition-Notification-To', MailTracker::getConfirmReturnPath($notification));
        });

        return parent::messageBuilder($notifiable, $notification, $message);
    }
}
