<?php

namespace App\Notifications;

use App\Events\MailStatusUpdated as Event;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use Illuminate\Broadcasting\Channel;
//Illuminate/Contracts/Queue/ShouldBeEncrypted
use Illuminate\Notifications\Channels\BroadcastChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class MailStatusUpdated extends Notification
{
    protected $mail;

    public function __construct(MailModel $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Get the notification channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return [BroadcastChannel::class];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function toBroadcast()
    {
        return [
            'id' => $this->mail->id,
            'delivery_status' => $this->mail->delivery_status,
            'updated_at' => $this->mail->updated_at
        ];
    }

    /**
     * Get the type of the notification being broadcast.
     *
     * @return string
     */
    public function broadcastType()
    {
        return 'mail.update';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('draw.'.$this->mail->mailable->draw->hash);
    }
}
