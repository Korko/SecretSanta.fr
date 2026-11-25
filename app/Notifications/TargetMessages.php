<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Contracts\WithDrawSecret;
use App\Models\DearSanta as DearSantaModel;
use App\Models\GiftSuggestion as GiftSuggestionModel;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;

// The santa's recap, in a single email sent again on request from their page: every message their target wrote to
// their santa, and the gift ideas others gave for that target. A new santa or a new address gets the same recap
// within the email telling them about their target (see viewData), never in more emails.
// Not tracked on purpose: the messages and ideas keep the status of their first delivery, so nothing moves on their
// senders' pages (a status starting over would tell them when that santa changed, or asked for their recap)
#[Backoff(30)]
#[Tries(10)]
final class TargetMessages extends Notification implements ShouldBeEncrypted, ShouldQueueAfterCommit, WithDrawSecret
{
    use Queueable;

    /**
     * Also used by TargetDrawn, TargetChanged and TargetWithdrawn: the recap comes with the target, in one email
     *
     * @return array{targetName: string, targetMessages: list<array{date: string, content: string}>, giftSuggestions: list<array{date: string, content: string}>}
     */
    public static function viewData(Participant $santa): array
    {
        $target = $santa->target;

        return [
            'targetName' => $target->name,
            // Queried, not the loaded relations: whatever was written until the email is rendered
            'targetMessages' => array_values($target->dearSantas()->get()->map(fn (DearSantaModel $dearSanta): array => [
                'date' => $dearSanta->mail->created_at?->isoFormat('LLL') ?? '',
                'content' => $dearSanta->mail_body,
            ])->all()),
            // But their own ideas, which they know already
            'giftSuggestions' => array_values($target->receivedSuggestions()->get()
                ->reject(fn (GiftSuggestionModel $suggestion): bool => $suggestion->sender_id === $santa->id)
                ->map(fn (GiftSuggestionModel $suggestion): array => [
                    'date' => $suggestion->mail->created_at?->isoFormat('LLL') ?? '',
                    'content' => $suggestion->mail_body,
                ])->all()),
        ];
    }

    // Nothing written to the santa nor given for their target yet: no recap to send
    public static function isEmpty(Participant $santa): bool
    {
        $recap = self::viewData($santa);

        return $recap['targetMessages'] === [] && $recap['giftSuggestions'] === [];
    }

    /** @return list<string> */
    public function via(Participant $santa): array
    {
        return ['mail'];
    }

    public function toMail(Participant $santa): MailMessage
    {
        return (new MailMessage)
            ->subject(__('emails.target_messages.title', ['draw' => $santa->draw->id]))
            ->view(['emails.target_messages', 'emails.target_messages_plain'], self::viewData($santa));
    }
}
