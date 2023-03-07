<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Mail\Mailable;
use Lang;

class TargetWithdrawn extends Mailable
{
    protected $santa;

    protected $oldTarget;

    protected $newTarget;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Participant $santa, Participant $oldTarget, Participant $newTarget)
    {
        $this->santa = $santa;
        $this->oldTarget = $oldTarget;
        $this->newTarget = $newTarget;
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
