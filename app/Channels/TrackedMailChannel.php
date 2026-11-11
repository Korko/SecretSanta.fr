<?php

declare(strict_types=1);

namespace App\Channels;

use App\Models\Draw;
use App\Models\Mail;
use App\Models\Participant;
use App\Notifications\TrackedNotification;
use App\Services\MailTracker;
use Closure;
use Illuminate\Mail\SentMessage;
use Illuminate\Notifications\Channels\MailChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use LogicException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;

final class TrackedMailChannel extends MailChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     */
    public function send($notifiable, Notification $notification): ?SentMessage
    {
        $mail = $this->mail($notifiable, $notification);

        try {
            $mail->markAsSending();

            $sent = parent::send($notifiable, $notification);

            $mail->markAsSent();

            return $sent;
        } catch (TransportExceptionInterface $transportException) {
            $mail->markAsError();
            throw $transportException;
        }
    }

    /**
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @param  MailMessage  $message
     */
    protected function messageBuilder($notifiable, $notification, $message): Closure
    {
        $mail = $this->mail($notifiable, $notification);
        $tracker = resolve(MailTracker::class);

        $message->withSymfonyMessage(function (Email $message) use ($mail, $tracker): void {
            // In case of Bounce
            $message->getHeaders()->addPathHeader('Return-Path', $tracker->getBounceReturnPath($mail));

            // To assert Reception
            $message->getHeaders()->addPathHeader('X-Confirm-Reading-To', $tracker->getConfirmReturnPath($mail));
            $message->getHeaders()->addPathHeader('Return-Receipt-To', $tracker->getConfirmReturnPath($mail));
            $message->getHeaders()->addPathHeader('Disposition-Notification-To', $tracker->getConfirmReturnPath($mail));
        });

        return parent::messageBuilder($notifiable, $notification, $message);
    }

    private function mail(mixed $notifiable, Notification $notification): Mail
    {
        // The organizer recap, sent to the draw itself (its organizer address)
        if ($notifiable instanceof Draw) {
            return $notifiable->recapMail();
        }

        throw_unless($notifiable instanceof Participant && $notification instanceof TrackedNotification, LogicException::class, 'Only tracked notifications to participants or draws go through this channel');

        return $notification->getMailableModel($notifiable)->mail;
    }
}
