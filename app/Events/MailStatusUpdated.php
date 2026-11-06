<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Mail as MailModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class MailStatusUpdated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(private MailModel $mail) {}

    /**
     * Get the data to broadcast.
     */
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
     * @return Channel|array
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('draw.'.$this->mail->mailable->draw->hash),
        ];
    }
}
