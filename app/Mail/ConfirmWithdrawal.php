<?php

namespace App\Mail;

use App\Models\Participant;
use Lang;

class ConfirmWithdrawal extends Mailable
{
    protected $santa;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Participant  $santa
     * @return void
     */
    public function __construct(Participant $santa)
    {
        $this->santa = $santa;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(Lang::get('emails.confirm_withdrawal.title', [
                'draw' => $this->santa->draw->id
            ]))
            ->markdown('emails.confirm_withdrawal', [
                'name' => $this->santa->name,
                'draw' => $this->santa->draw->id,
                'organizerName' => $this->santa->draw->organizer_name,
            ]);
    }
}