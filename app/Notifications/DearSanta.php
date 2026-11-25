<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Channels\TrackedMailChannel;
use App\Contracts\WithDrawSecret;
use App\Models\DearSanta as DearSantaModel;
use App\Models\Draw;
use App\Models\Mail;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\SerializesModels;

#[Backoff(30)]
#[Tries(10)]
final class DearSanta extends Notification implements ShouldBeEncrypted, ShouldQueueAfterCommit, WithDrawSecret, TrackedNotification
{
    use Queueable;
    use SerializesModels;

    public function __construct(private DearSantaModel $dearSanta) {}

    /**
     * Get the notification's delivery channels.
     */
    /** @return list<string> */
    public function via(Participant $santa): array
    {
        return [TrackedMailChannel::class];
    }

    public function getTrackedMail(Participant|Draw $notifiable): Mail
    {
        return $this->dearSanta->mail;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Participant $santa): MailMessage
    {
        $url = $santa->pageUrl();

        return (new MailMessage)
            ->subject(__('emails.dear_santa.title', ['draw' => $santa->draw->id]))
            ->view(['emails.dearsanta', 'emails.dearsanta_plain'], [
                'content' => $this->dearSanta->mail_body,
                'targetName' => $santa->target->name,
                'dearSantaLink' => $url,
            ]);
    }
}
