<?php

namespace App\Mail;

use App\Facades\DrawCrypt;
use App\Models\DearTarget as DearTargetModel;
use App\Models\Participant;
use Illuminate\Support\Facades\URL;
use Lang;

class DearTarget extends TrackedMailable
{
    protected $target;
    protected $dearTarget;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Participant  $target
     * @param  \App\Models\DearTarget  $dearTarget
     * @return void
     */
    public function __construct(Participant $target, DearTargetModel $dearTarget)
    {
        $this->target = $target;
        $this->dearTarget = $dearTarget;
    }

    protected function getMailable()
    {
        return $this->dearTarget;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = URL::signedRoute('target.index', ['participant' => $this->target->hash]).'#'.base64_encode(DrawCrypt::getIV());

        return $this
            ->subject(Lang::get('SecretSanta #:draw - Message de votre père/mère noël secrêt(e)', ['draw' => $this->target->draw->id]))
            ->markdown('emails.dearTarget', [
                'name' => $this->target->name,
                'draw' => $this->target->draw->id,
                'content' => $this->dearTarget->mail_body,
                'santaName' => $this->target->santa->name,
                'dearSantaLink' => $url
            ]);
    }
}
