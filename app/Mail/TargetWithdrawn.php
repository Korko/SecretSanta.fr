<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Mail\Mailable;
use Lang;

class TargetWithdrawn extends Mailable
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
            ->subject(Lang::get('SecretSanta #:draw - Desistement du bénéficiaire de votre cadeau', [
                'draw' => $this->santa->draw->id
            ]))
            ->markdown('emails.target_withdrawn', [
                'name' => $this->santa->name,
                'draw' => $this->santa->draw->id,
                'targetName' => $this->santa->target->name,
            ]);
    }
}
