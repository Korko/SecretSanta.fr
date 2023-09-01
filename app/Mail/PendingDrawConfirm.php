<?php

namespace App\Mail;

use App\Models\PendingDraw as PendingDrawModel;
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
        protected readonly PendingDrawModel $pendingDraw
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: Lang::get('SecretSanta - Attente de validation', ['draw' => $this->pendingDraw->hash]),
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
                'name' => $this->pendingDraw->organizer_name,
                'validationLink' => URL::hashedSignedRoute('pending.confirmOrganizerEmail', ['pendingDraw' => $this->pendingDraw->hash]),
            ]
        );
    }
}
