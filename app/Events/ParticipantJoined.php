<?php

namespace App\Events;

use App\Models\Draw\Participant;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foadation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantJoined implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public Participant $participant;
    private string $encryptedName;

    public function __construct(Participant $participant, string $encryptedName)
    {
        $this->participant = $participant;
        $this->encryptedName = $encryptedName;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('draw.' . $this->participant->draw->uuid);
    }

    public function broadcastAs(): string
    {
        return 'participant.added';
    }

    public function broadcastWith(): array
    {
        return [
            'participant' => [
                'uuid' => $this->participant->uuid,
                'name' => $this->encryptedName,
                'status' => $this->participant->status,
                'joined_at' => $this->participant->created_at->toIso8601String(),
            ],
        ];
    }
}
