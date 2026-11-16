<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\DearSanta;
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
                // A participant follows the messages they wrote to their santa
                $mailable instanceof DearSanta => $mailable->sender->broadcastChannel(),
                default => throw new LogicException('Mail without a participant or dearSanta'),
            }),
        ];
    }
}
