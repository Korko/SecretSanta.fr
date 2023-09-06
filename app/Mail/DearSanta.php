<?php

namespace App\Mail;

use App\Models\DearSanta as DearSantaModel;
use App\Models\Participant;
use Illuminate\Support\Facades\URL;
use Lang;

class DearSanta extends TrackedMailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected readonly Participant $santa,
        protected readonly DearSantaModel $dearSanta
    ) {
    }

    protected function getMailable()
    {
        return $this->dearSanta;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject(Lang::get('SecretSanta #:draw - Message du bénéficiaire de votre cadeau', ['draw' => $this->santa->draw->id]))
            ->markdown('emails.dearsanta', [
                'name' => $this->santa->name,
                'draw' => $this->santa->draw->id,
                'content' => $this->dearSanta->mail_body,
                'targetName' => $this->dearSanta->sender->name,
                'dearSantaLink' => URL::hashedSignedRoute('santa.index', ['participant' => $this->santa->id]),
                'reportLink' => URL::hashedSignedRoute('report', ['participant' => $this->santa->id]),
            ]);
    }
}
