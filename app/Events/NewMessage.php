<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Participant;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foadation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public Participant $recipient;
    private string $encryptedContent;

    public function __construct(Message $message, Participant $recipient, string $encryptedContent)
    {
        $this->message = $message;
        $this->recipient = $recipient;
        $this->encryptedContent = $encryptedContent;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('participant.' . $this->recipient->uuid),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.new';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'content' => $this->encryptedContent,
            'type' => $this->message->type,
            'from' => $this->message->type === 'to_secret_santa'
                ? 'Votre cible'
                : 'Votre Secret Santa',
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }

    public function broadcastWhen(): bool
    {
        // Ne pas broadcaster if the message is signalé
        return !$this->message->is_reported;
    }
}
