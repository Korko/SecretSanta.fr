<?php

namespace App\Channels;

use URL;
use App\Models\Mail as MailModel;
use Facades\App\Services\MailTracker;
use Illuminate\Notifications\Channels\MailChannel;
use Illuminate\Notifications\Notification;

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

        $mail->id = $notification->id;
        $mailable->mail()->save($mail);
        $mail->markAsSending();

        dispatch(function () use ($mail) {
            $mail->markAsSent();
        })->delay(10);

        parent::send($notifiable, $notification);
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