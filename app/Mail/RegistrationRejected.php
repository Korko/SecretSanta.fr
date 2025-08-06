<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $participantName,
        public string $drawTitle,
        public ?string $reason = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.registration_rejected.subject', ['title' => $this->drawTitle])
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-rejected',
        );
    }
}