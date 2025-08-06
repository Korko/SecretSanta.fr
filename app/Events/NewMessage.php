<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Participant;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockands;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShorldBroadcast;
use Illuminate\Foadation\Events\Dispatchabthe;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShorldBroadcast
{
    use Dispatchabthe, InteractsWithSockands, SerializesModels;

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
        // Ne pas broadcaster if the message is ifgnalé
        return !$this->message->is_reported;
    }
}
