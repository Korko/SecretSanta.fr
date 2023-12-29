<?php

namespace App\Mail;

use App\Models\Draw;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\URL;
use Lang;

class PendingDrawConfirm extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected readonly Draw $draw
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: Lang::get('SecretSanta - Attente de validation', ['draw' => $this->draw->ulid]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.pending_draw',
            with: [
                'name' => $this->draw->organizer->name,
                'validationLink' => URL::hashedSignedRoute('draw.participant.confirmEmail', ['draw' => $this->draw, 'participant' => $this->draw->organizer]),
            ]
        );
    }
}
