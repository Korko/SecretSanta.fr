<?php

namespace App\Mail;

use App\Models\Participant;
use Lang;

class TargetNameChanged extends Mailable
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
            ->subject(Lang::get('SecretSanta #:draw - Votre cible a changé de nom', [
                'draw' => $this->santa->draw->id
            ]))
            ->markdown('emails.name_changed', [
                'name' => $this->santa->name,
                'draw' => $this->santa->draw->id,
                'targetName' => $this->santa->target->name,
            ]);
    }
}