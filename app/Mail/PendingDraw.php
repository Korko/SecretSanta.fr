<?php

namespace App\Mail;

use App\Models\PendingDraw as PendingDrawModel;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Lang;

class PendingDraw extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected readonly PendingDrawModel $draw
    ) {
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject(Lang::get('SecretSanta - Attente de validation', ['draw' => $this->draw->id]))
            ->markdown('emails.pending_draw', [
                'name' => $this->draw->organizer_name,
                'validationLink' => URL::hashedSignedRoute('pending.view', ['pending' => $this->draw->id]),
            ]);
    }
}
