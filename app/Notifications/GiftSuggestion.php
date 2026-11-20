<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Channels\TrackedMailChannel;
use App\Facades\DrawCrypt;
use App\Models\Draw;
use App\Models\GiftSuggestion as GiftSuggestionModel;
use App\Models\Mail;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

// To the santa of the person the idea is for (see GiftSuggestionModel)
final class GiftSuggestion extends Notification implements ShouldBeEncrypted, ShouldQueueAfterCommit, TrackedNotification
{
    use Queueable;
    use SerializesModels;

    public function __construct(private GiftSuggestionModel $suggestion) {}

    /** @return list<string> */
    public function via(Participant $santa): array
    {
        return [TrackedMailChannel::class];
    }

    public function getTrackedMail(Participant|Draw $notifiable): Mail
    {
        return $this->suggestion->mail;
    }

    public function toMail(Participant $santa): MailMessage
    {
        $url = URL::signedRoute('dearSanta', ['participant' => $santa->hash]).'#'.base64_encode(DrawCrypt::getIV());

        return (new MailMessage)
            ->subject(__('emails.gift_suggestion.title', ['draw' => $santa->draw->id, 'name' => $this->suggestion->recipient->name]))
            ->view(['emails.gift_suggestion', 'emails.gift_suggestion_plain'], [
                'content' => $this->suggestion->mail_body,
                'targetName' => $this->suggestion->recipient->name,
                'senderName' => $this->suggestion->signed ? $this->suggestion->sender->name : null,
                'dearSantaLink' => $url,
            ]);
    }
}
