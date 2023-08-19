<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Mail\Mailable;
use Lang;

class TargetWithdrawn extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected readonly Participant $santa,
        protected readonly Participant $oldTarget,
        protected readonly Participant $newTarget
    ) {
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject(Lang::get('SecretSanta #:draw - Desistement du bénéficiaire de votre cadeau', [
                'draw' => $this->santa->draw->id,
            ]))
            ->markdown('emails.target_withdrawn', [
                'name' => $this->santa->name,
                'draw' => $this->santa->draw->id,
                'oldTargetName' => $this->oldTarget->name,
                'newTargetName' => $this->newTarget->name,
            ]);
    }
}
