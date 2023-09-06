<?php

namespace App\Mail;

use App\Models\DearTarget as DearTargetModel;
use App\Models\Participant;
use Illuminate\Support\Facades\URL;
use Lang;

class DearTarget extends TrackedMailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected readonly Participant $target,
        protected readonly DearTargetModel $dearTarget
    ) {
    }

    protected function getMailable()
    {
        return $this->dearTarget;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject(Lang::get('SecretSanta #:draw - Message de votre père/mère noël secrêt(e)', ['draw' => $this->target->draw->id]))
            ->markdown('emails.dearTarget', [
                'name' => $this->target->name,
                'draw' => $this->target->draw->id,
                'content' => $this->dearTarget->mail_body,
                'santaName' => $this->target->santa->name,
                'dearSantaLink' => URL::hashedSignedRoute('santa.index', ['participant' => $this->target->id]),
                'reportLink' => URL::hashedSignedRoute('report', ['participant' => $this->target->id]),
            ]);
    }
}
