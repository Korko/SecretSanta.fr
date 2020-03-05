<?php

namespace App\Mail;

use App\DearSanta as DearSantaEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class DearSanta extends Mailable
{
    use Queueable, UpdatesDeliveryStatus;

    public $targetName;
    public $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(DearSantaEntry $dearSanta)
    {
        $this->subject = __('emails.dear_santa.title', ['draw' => $dearSanta->sender->draw->id]);

        $this->content = $dearSanta->mail_body;

        $this->targetName = $dearSanta->sender->name;

        $this->trackMail($dearSanta->mail);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->withSwiftMessage(function ($message) {
            $connection = config('hashids.parameters')['bounce'];
            $hash = Hashids::connection($connection)->encode($this->getMailId());

            $message->getHeaders()
                    ->addPathHeader('Return-Path', str_replace('*', $hash, config('mail.return_path')));
        });

        return $this->view('emails.dearsanta')
                    ->text('emails.dearsanta_plain');
    }
}
