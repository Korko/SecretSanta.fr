<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Contracts\WithDrawSecret;
use App\Enums\ReportReason;
use App\Models\Draw;
use DrawCrypt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

// Sent to the organizer when a participant reports their target email: a mistyped address is best fixed
// right away, before the right person thinks they were forgotten
#[Backoff(30)]
#[Tries(10)]
final class ParticipantReported extends Notification implements ShouldBeEncrypted, ShouldQueueAfterCommit, WithDrawSecret
{
    use Queueable;

    public function __construct(private readonly string $name, private readonly string $email, private readonly ReportReason $reason) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(Draw $draw): MailMessage
    {
        return (new MailMessage)
            ->subject(Lang::get('emails.participant_reported.title', ['draw' => $draw->id, 'name' => $this->name]))
            ->view(['emails.participant_reported', 'emails.participant_reported_plain'], [
                'organizerName' => $draw->organizer_name,
                'name' => $this->name,
                'email' => $this->email,
                'reason' => $this->reason->value,
                'panelLink' => URL::signedRoute('organizerPanel', ['draw' => $draw->hash]).'#'.base64_encode(DrawCrypt::getSecret()),
            ]);
    }
}
