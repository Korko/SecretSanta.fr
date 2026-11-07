<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Draw;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Support\Facades\Lang;

// Sent to the previous organizer address when /fix replaces it: if it wasn't them, their panel link still works
#[Backoff(30)]
#[Tries(10)]
final class OrganizerEmailChanged extends Notification implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Draw $draw, private readonly string $newEmail) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return list<string>
     */
    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(Lang::get('emails.organizer_email_changed.title', ['draw' => $this->draw->id]))
            ->view(['emails.organizer_email_changed', 'emails.organizer_email_changed_plain'], [
                'organizerName' => $this->draw->organizer_name,
                'newEmail' => $this->newEmail,
            ]);
    }
}
