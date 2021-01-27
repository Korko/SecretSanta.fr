<?php

namespace App\Channels;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Notifications\Channels\MailChannel as BaseMailChannel;
use Illuminate\Notifications\Notification;

class MailChannel extends BaseMailChannel
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
        $message = $notification->toMail($notifiable);

        // Fix bug in Laravel 8 about Mailable not being pushed in Mail::fake
        if ($message instanceof Mailable) {
            return $this->mailer->send($message);
        }

        return parent::send($notifiable, $notification);
    }
}