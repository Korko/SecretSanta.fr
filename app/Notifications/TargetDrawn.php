<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Channels\TrackedMailChannel;
use App\Contracts\WithDrawSecret;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\MaxExceptions;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Support\Facades\Lang;

#[Backoff(30)]
#[MaxExceptions(10)]
#[Tries(10)]
final class TargetDrawn extends Notification implements ShouldBeEncrypted, ShouldQueueAfterCommit, TrackedNotification, WithDrawSecret
{
    use Queueable;
    use TracksSantaMail;

    /**
     * Get the notification's delivery channels.
     */
    /** @return list<string> */
    public function via(Participant $santa): array
    {
        return [TrackedMailChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Participant $santa): MailMessage
    {
        $title = $this->parseKeywords((string) Lang::get('emails.target_draw.title', [
            'draw' => $santa->draw->id,
            'subject' => $santa->draw->mail_title,
        ]), $santa);

        $content = $this->parseKeywords($santa->draw->mail_body, $santa);

        $url = $santa->pageUrl();

        $message = (new MailMessage)
            ->subject($title)
            ->view(['emails.target_drawn', 'emails.target_drawn_plain'], [
                'content' => $content,
                'dearSantaLink' => $url,
                ...OrganizerRecap::eventDetails($santa->draw),
                // A new santa or a new address: what their target already wrote or was given comes in this same email
                ...TargetMessages::viewData($santa),
                // Their link above already opens their panel
                'organizer' => $santa->isOrganizer() ? [...OrganizerRecap::viewData($santa->draw), 'samePage' => true] : null,
            ]);

        // The organizer taking part gets a single email: their target + the organizer recap
        return $santa->isOrganizer()
            ? $message->attachData($santa->draw->initialCsv(), $santa->draw->initialCsvName(), ['mime' => 'text/csv'])
            : $message;
    }

    private function parseKeywords(string $str, Participant $santa): string
    {
        return str_ireplace(['{SANTA}', '{TARGET}'], [$santa->name, $santa->target->name], $str);
    }
}
