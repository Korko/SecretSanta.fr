<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Mail\Mailable;
use Lang;

class ConfirmWithdrawal extends Mailable
{
    protected $santa;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Participant $santa)
    {
        $this->santa = $santa;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject(Lang::get('SecretSanta #:draw - Confirmation de désistement', [
                'draw' => $this->santa->draw->id,
            ]))
            ->markdown('emails.confirm_withdrawal', [
                'name' => $this->santa->name,
                'draw' => $this->santa->draw->id,
                'organizerName' => $this->santa->draw->organizer_name,
            ]);
    }
}
