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
            ->subject(Lang::get('SecretSanta #:draw - Message de votre père/mère noël secrêt(e)', ['draw' => $this->target->draw->ulid]))
            ->markdown('emails.dearTarget', [
                'name' => $this->target->name,
                'draw' => $this->target->draw->ulid,
                'content' => $this->dearTarget->mail_body,
                'santaName' => $this->target->santa->name,
                'dearSantaLink' => URL::hashedRoute('participant.index', ['participant' => $this->target]),
                'reportLink' => URL::hashedRoute('report.index', ['participant' => $this->target]),
            ]);
    }
}
