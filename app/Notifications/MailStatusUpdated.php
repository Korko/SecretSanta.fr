<?php

namespace App\Notifications;

use App\Models\Mail as MailModel;
use App\Models\Participant;
//Illuminate/Contracts/Queue/ShouldBeEncrypted
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class MailStatusUpdated extends Notification
{
    protected $mail;

    public function __construct(MailModel $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  App\Models\Participant  $santa
     * @return array
     */
    public function via(Participant $santa)
    {
        return [WebPushChannel::class];
    }

    public function getMailableModel(Participant $santa)
    {
        return $this->dearSanta;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  App\Models\Participant  $santa
     * @return \Illuminate\Mail\Mailable
     */
    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->topic('mail.update')
            ->data([
                'id' => $this->mail->id,
                'delivery_status' => $this->mail->delivery_status,
                'updated_at' => $this->mail->updated_at
            ])
            ->options(['TTL' => 1000]);
    }
}
