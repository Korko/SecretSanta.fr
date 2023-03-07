<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Mail\Mailable;
use Lang;

class TargetNameChanged extends Mailable
{
    protected $santa;

    protected $target;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Participant $santa, Participant $target)
    {
        $this->santa = $santa;
        $this->target = $target;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject(Lang::get('SecretSanta #:draw - Votre cible a changé de nom', [
                'draw' => $this->santa->draw->id,
            ]))
            ->markdown('emails.name_changed', [
                'name' => $this->santa->name,
                'draw' => $this->santa->draw->id,
                'targetName' => $this->target->name,
            ]);
    }
}
