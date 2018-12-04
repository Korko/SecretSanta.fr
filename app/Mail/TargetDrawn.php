<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class TargetDrawn extends Mailable
{
    use Queueable, SerializesModels, SendGrid;

    public $santa;
    public $target;

    public $subject;
    public $content;

    public $organizer;

    public $dearSantaLink;

    public $customArgs;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($santa, $target, $subject, $content, $organizer, $dearSantaLink = null, $customArgs = [])
    {
        $this->santa = $santa;
        $this->target = $target;
        $this->subject = $subject;
        $this->content = $content;
        $this->organizer = $organizer;
        $this->dearSantaLink = $dearSantaLink;
        $this->customArgs = $customArgs;
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
                    ->text('emails.target_drawn_plain')
                    ->sendgrid([
                        'custom_args' => [
                            'data' => json_encode($this->customArgs + [
                                'organizer'     => $this->organizer,
                                'santa'         => $this->santa,
                                'target'        => $this->target,
                                'subject'       => $this->subject,
                                'content'       => $this->content,
                                'dearSantaLink' => $this->dearSantaLink,
                            ]),
                        ],
                    ]);

    }
}
