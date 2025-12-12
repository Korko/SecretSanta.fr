<?php

namespace App\Notifications;

use App\Channels\TrackedMailChannel;
use App\Models\Participant;
use DrawCrypt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;

class TargetDrawn extends Notification implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 10;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 10;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 30;

    /**
     * Get the notification's delivery channels.
     */
    public function via(Participant $santa): array
    {
        return [TrackedMailChannel::class];
    }

    public function getMailableModel(Participant $santa)
    {
        return $santa;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Participant $santa): Mailable
    {
        $title = $this->parseKeywords(Lang::get('emails.target_draw.title', [
            'draw' => $santa->draw->id,
            'subject' => $santa->draw->mail_title,
        ]), $santa);

        $content = $this->parseKeywords($santa->draw->mail_body, $santa);

        $url = URL::signedRoute('dearSanta', ['participant' => $santa->hash]).'#'.base64_encode(DrawCrypt::getIV());

        return (new MailMessage)
            ->subject($title)
            ->view(['emails.target_drawn', 'emails.target_drawn_plain'], [
                'content' => $content,
                'dearSantaLink' => $url,
            ]);
    }

    protected function parseKeywords($str, Participant $santa)
    {
        return str_ireplace(['{SANTA}', '{TARGET}'], [$santa->name, $santa->target->name], $str);
    }
}
