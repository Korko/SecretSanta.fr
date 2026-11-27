<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\DearSanta;
use App\Models\Draw;
use App\Models\GiftSuggestion;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use LogicException;

// Not for a transaction rolled back after all (OrganizerController::restore, FixOrganizerController)
final class MailStatusUpdated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(private MailModel $mail) {}

    /**
     * Get the data to broadcast.
     */
    /** @return array<string, mixed> */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->mail->id,
            'delivery_status' => $this->mail->delivery_status,
            'bounce_reason' => $this->mail->bounce_reason,
            'updated_at' => $this->mail->updated_at,
        ];
    }

    // The sender of a message or a gift idea only learns whether it was sent (see DearSantaController::senderMail)
    public function broadcastWhen(): bool
    {
        $mailable = $this->mail->mailable;

        if (! $mailable instanceof DearSanta && ! $mailable instanceof GiftSuggestion) {
            return true;
        }

        // Nobody to tell either once its sender left the draw
        return $mailable->sender !== null && $this->mail->delivery_status->isVisibleToSender();
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'mail.update';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return list<Channel>
     */
    public function broadcastOn(): array
    {
        $mailable = $this->mail->mailable;

        return [
            new PrivateChannel(match (true) {
                // The organizer panel follows the target emails
                $mailable instanceof Participant => $mailable->draw->broadcastChannel(),
                // The organizer panel follows the recap too, for an organizer who doesn't take part
                $mailable instanceof Draw => $mailable->broadcastChannel(),
                // A participant follows the messages they wrote to their santa
                $mailable instanceof DearSanta => $mailable->sender->broadcastChannel(),
                // And the gift ideas they gave
                $mailable instanceof GiftSuggestion && $mailable->sender instanceof Participant => $mailable->sender->broadcastChannel(),
                default => throw new LogicException('Mail without a participant, dearSanta, gift suggestion or draw'),
            }),
        ];
    }
}
