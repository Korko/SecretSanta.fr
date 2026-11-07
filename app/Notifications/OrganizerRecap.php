<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Draw;
use App\Models\Participant;
use DrawCrypt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\MaxExceptions;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

#[Backoff(30)]
#[MaxExceptions(10)]
#[Tries(20)]
final class OrganizerRecap extends Notification implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(private Draw $draw) {}

    /**
     * Also used by TargetDrawn when the organizer takes part in the draw
     *
     * @return array<string, mixed>
     */
    public static function viewData(Draw $draw): array
    {
        return [
            'organizerName' => $draw->organizer_name,
            'expirationDate' => $draw->expires_at->isoFormat('LL'),
            'deletionDate' => $draw->deleted_at->isoFormat('LL'),
            'nextSolvable' => $draw->next_solvable,
            'panelLink' => URL::signedRoute('organizerPanel', ['draw' => $draw->hash]).'#'.base64_encode(DrawCrypt::getIV()),
        ];
    }

    /** @return list<string> */
    public function via(AnonymousNotifiable|Participant $organizer): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  AnonymousNotifiable|Participant  $organizer
     */
    public function toMail($organizer): MailMessage
    {
        return (new MailMessage)
            ->subject(__('emails.organizer_recap_title', ['draw' => $this->draw->id]))
            ->view(['emails.organizer_recap', 'emails.organizer_recap_plain'], self::viewData($this->draw))
            ->attachData($this->draw->initialCsv(), $this->draw->initialCsvName(), ['mime' => 'text/csv']);
    }
}
