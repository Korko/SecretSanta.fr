<?php

namespace App\Events;

use App\Models\Mail as MailModel;
use App\Models\Participant;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TargetDrawnMailStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $mail;
    protected $participant;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Participant $participant, MailModel $mail)
    {
        $this->mail = $mail;
        $this->participant = $participant;
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'mail.update';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->participant->id,
            'delivery_status' => $this->mail->delivery_status,
            'updated_at' => $this->mail->updated_at
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('draw.'.$this->participant->draw->hash);
    }
}