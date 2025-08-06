<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationRequest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $organizerName,
        public string $participantName,
        public string $participantEmail,
        public string $drawTitle,
        public string $managementLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.registration_request.subject', ['name' => $this->participantName])
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-request',
        );
    }
}