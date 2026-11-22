<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Participant;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

// Something new for a participant's page (a message or gift idea they sent from another tab, an idea received
// for their target): no content on the socket, the page refetches it through its own secret link (fetchState)
final readonly class ParticipantUpdated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable;

    // The channel only: the participant may be gone (withdrawn) by the time the queue sends it
    private string $channel;

    public function __construct(Participant $participant)
    {
        $this->channel = $participant->broadcastChannel();
    }

    /** @return array<string, mixed> */
    public function broadcastWith(): array
    {
        return [];
    }

    public function broadcastAs(): string
    {
        return 'participant.update';
    }

    /** @return list<Channel> */
    public function broadcastOn(): array
    {
        return [new Channel($this->channel)];
    }
}
