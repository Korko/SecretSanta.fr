<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $drawTitle,
        public string $messageType,
        public string $participantLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.message_notification.subject', ['title' => $this->drawTitle])
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.message-notification',
        );
    }
}