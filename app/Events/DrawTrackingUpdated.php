<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\DrawTracking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

// The draw was solved, or not: to the browser that sent the form only (private channel, authorized by its token)
final readonly class DrawTrackingUpdated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable;

    private string $channel;

    /** @var array<string, mixed> */
    private array $details;

    // Computed now: the tracking (and the draw) may be gone once the event is broadcast
    public function __construct(DrawTracking $tracking)
    {
        $this->channel = $tracking->broadcastChannel();
        $this->details = $tracking->details();
    }

    /** @return array<string, mixed> */
    public function broadcastWith(): array
    {
        return $this->details;
    }

    public function broadcastAs(): string
    {
        return 'tracking.update';
    }

    /** @return list<Channel> */
    public function broadcastOn(): array
    {
        return [new PrivateChannel($this->channel)];
    }
}
