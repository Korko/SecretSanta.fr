<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Mail\Mailable;
use Lang;

class TargetNameChanged extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected readonly Participant $santa,
        protected readonly Participant $target
    ) {
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject(Lang::get('SecretSanta #:draw - Votre cible a changé de nom', [
                'draw' => $this->santa->draw->ulid,
            ]))
            ->markdown('emails.name_changed', [
                'name' => $this->santa->name,
                'draw' => $this->santa->draw->ulid,
                'targetName' => $this->target->name,
            ]);
    }
}
