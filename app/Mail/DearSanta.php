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
            ->subject(Lang::get('SecretSanta #:draw - Message du bénéficiaire de votre cadeau', ['draw' => $this->santa->draw->ulid]))
            ->markdown('emails.dearsanta', [
                'name' => $this->santa->name,
                'draw' => $this->santa->draw->ulid,
                'content' => $this->dearSanta->mail_body,
                'targetName' => $this->dearSanta->sender->name,
                'dearSantaLink' => URL::hashedRoute('participant.index', ['participant' => $this->santa]),
                'reportLink' => URL::hashedRoute('report.index', ['participant' => $this->santa]),
            ]);
    }
}
