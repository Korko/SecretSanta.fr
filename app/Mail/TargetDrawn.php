<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class TargetDrawn extends Mailable
{
    use Queueable, SerializesModels, SendGrid;

    public $subject;
    public $content;
    public $personalizations;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $content, $personalizations)
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->personalizations = $personalizations;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->sendgrid([
                        'personalizations' => $this->personalizations,
                    ])
                    ->view('emails.target_drawn')
                    ->text('emails.target_drawn_plain');
    }
}
