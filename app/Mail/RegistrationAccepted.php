<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationAccepted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $participantName,
        public string $drawTitle,
        public string $organizerName
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.registration_accepted.subject', ['title' => $this->drawTitle])
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-accepted',
        );
    }
}