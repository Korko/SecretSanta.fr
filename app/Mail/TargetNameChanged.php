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
            ->subject(Lang::get('emails.name_changed.title', [
                'draw' => $this->santa->draw->id
            ]))
            ->view(['emails.name_changed', 'emails.name_changed_plain'], [
                'draw' => $this->santa->draw->id,
                'santaName' => $this->santa->name,
                'targetName' => $this->santa->target->name,
            ]);
    }
}