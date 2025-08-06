<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DrawFailed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $organizerName,
        public string $drawTitle,
        public string $reason,
        public string $organizerLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.draw_failed.subject', ['title' => $this->drawTitle])
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.draw-failed',
        );
    }
}