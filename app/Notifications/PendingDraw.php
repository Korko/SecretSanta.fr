<?php

namespace App\Notifications;

use App\Mail\PendingDraw as PendingDrawMailable;
use App\Models\PendingDraw as PendingDrawModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class PendingDraw extends Notification implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 20;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 30;

    protected $draw;

    public function __construct(PendingDrawModel $draw)
    {
        $this->draw = $draw;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  \Illuminate\Notifications\AnonymousNotifiable $organizer
     * @return array
     */
    public function via($organizer)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  \Illuminate\Notifications\AnonymousNotifiable $organizer
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail($organizer)
    {
        return (new PendingDrawMailable($this->draw))
            ->to($organizer->routeNotificationFor('mail'));
    }
}