<?php

namespace App\Events;

use App\Models\PendingDraw;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PendingDrawStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        protected readonly PendingDraw $draw
    ) {
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->draw->id,
            'status' => $this->draw->status,
            'updated_at' => $this->draw->updated_at,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'status.update';
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('pending_draw.'.$this->draw->id);
    }
}
