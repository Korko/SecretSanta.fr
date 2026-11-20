<?php

declare(strict_types=1);

namespace App\Events;

use App\Enums\DeliveryStatus;
use App\Models\DearSanta;
use App\Models\Draw;
use App\Models\GiftSuggestion;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use LogicException;

final class MailStatusUpdated implements ShouldBroadcast
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
            'updated_at' => $this->mail->updated_at,
        ];
    }

    // A gift idea's sender only learns whether it was sent (see DearSantaController::publicSuggestion)
    public function broadcastWhen(): bool
    {
        return ! $this->mail->mailable instanceof GiftSuggestion || $this->mail->delivery_status !== DeliveryStatus::Received;
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
            new Channel(match (true) {
                // The organizer panel follows the target emails
                $mailable instanceof Participant => $mailable->draw->broadcastChannel(),
                // The organizer panel follows the recap too, for an organizer who doesn't take part
                $mailable instanceof Draw => $mailable->broadcastChannel(),
                // A participant follows the messages they wrote to their santa
                $mailable instanceof DearSanta => $mailable->sender->broadcastChannel(),
                // And the gift ideas they gave
                $mailable instanceof GiftSuggestion => $mailable->sender->broadcastChannel(),
                default => throw new LogicException('Mail without a participant, dearSanta, gift suggestion or draw'),
            }),
        ];
    }
}
