<?php

namespace App\Events;

use App\Models\DearSanta;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DearSantaMailStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $dearSanta;
    protected $mail;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(DearSanta $dearSanta, MailModel $mail)
    {
        $this->dearSanta = $dearSanta;
        $this->mail = $mail;
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
            'id' => $this->dearSanta->id,
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
        return new Channel('participant.'.$this->dearSanta->sender->hash);
    }
}