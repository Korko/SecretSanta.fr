<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Channels\TrackedMailChannel;
use App\Models\Participant;
use DrawCrypt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\MaxExceptions;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

#[Backoff(30)]
#[MaxExceptions(10)]
#[Tries(10)]
final class TargetDrawn extends Notification implements ShouldBeEncrypted, ShouldQueue, TrackedNotification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     */
    public function via(Participant $santa): array
    {
        return [TrackedMailChannel::class];
    }

    public function getMailableModel(Participant $santa): Participant
    {
        return $santa;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Participant $santa): MailMessage|Mailable
    {
        $title = $this->parseKeywords(Lang::get('emails.target_draw.title', [
            'draw' => $santa->draw->id,
            'subject' => $santa->draw->mail_title,
        ]), $santa);

        $content = $this->parseKeywords($santa->draw->mail_body, $santa);

        $url = URL::signedRoute('dearSanta', ['participant' => $santa->hash]).'#'.base64_encode(DrawCrypt::getIV());

        $message = (new MailMessage)
            ->subject($title)
            ->view(['emails.target_drawn', 'emails.target_drawn_plain'], [
                'content' => $content,
                'dearSantaLink' => $url,
                'organizer' => $santa->isOrganizer() ? OrganizerRecap::viewData($santa->draw) : null,
            ]);

        // The organizer taking part gets a single email: their target + the organizer recap
        return $santa->isOrganizer()
            ? $message->attachData($santa->draw->initialCsv(), $santa->draw->initialCsvName(), ['mime' => 'text/csv'])
            : $message;
    }

    private function parseKeywords($str, Participant $santa): string|array
    {
        return str_ireplace(['{SANTA}', '{TARGET}'], [$santa->name, $santa->target->name], $str);
    }
}
