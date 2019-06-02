<?php

namespace App\Mail;

use App\Draw;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class TargetDrawn extends Mailable
{
    use Queueable, SerializesModels, SendGrid;

    public $subject;

    public $content;
    public $dearSantaLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Draw $draw, $subject, $content, $santaName, $targetName, $dearSantaLink = null)
    {
        $this->subject = __('emails.target_draw.title', ['draw' => $draw->id, 'subject' => $subject]);
        $this->content = str_replace('{SANTA}', $santaName, str_replace('{TARGET}', $targetName, $content));
        $this->dearSantaLink = $dearSantaLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.target_drawn')
                    ->text('emails.target_drawn_plain');
    }
}
