<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\DearSanta as DearSantaModel;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;

// Every message the santa's target wrote to their santa, in a single email: to a new santa, or sent again on request.
// Not tracked on purpose: the messages keep the status of their first delivery, so nothing moves on the target's
// page (a status starting over would tell them when their santa changed, or asked for their messages)
#[Backoff(30)]
#[Tries(10)]
final class TargetMessages extends Notification implements ShouldBeEncrypted, ShouldQueueAfterCommit
{
    use Queueable;

    /** @return list<string> */
    public function via(Participant $santa): array
    {
        return ['mail'];
    }

    public function toMail(Participant $santa): MailMessage
    {
        return (new MailMessage)
            ->subject(__('emails.target_messages.title', ['draw' => $santa->draw->id]))
            ->view(['emails.target_messages', 'emails.target_messages_plain'], [
                'targetName' => $santa->target->name,
                'messages' => $santa->target->dearSantas->map(fn (DearSantaModel $dearSanta): array => [
                    'date' => $dearSanta->mail->created_at?->isoFormat('LLL') ?? '',
                    'content' => $dearSanta->mail_body,
                ])->all(),
            ]);
    }
}
