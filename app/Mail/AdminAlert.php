<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $level,
        public string $message,
        public array $context = []
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.admin_alert.subject', ['level' => strtoupper($this->level)])
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-alert',
        );
    }
}