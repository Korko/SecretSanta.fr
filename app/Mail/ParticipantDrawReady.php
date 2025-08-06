<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ParticipantDrawReady extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $participantName,
        public string $drawTitle,
        public string $participantLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.participant_draw_ready.subject', ['title' => $this->drawTitle])
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.participant-draw-ready',
        );
    }
}