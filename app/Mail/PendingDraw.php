<?php

namespace App\Mail;

use App;
use App\Facades\DrawCrypt;
use App\Models\PendingDraw as PendingDrawModel;
use Illuminate\Support\Facades\URL;
use Lang;

class PendingDraw extends Mailable
{
    protected $draw;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\PendingDraw  $draw
     * @return void
     */
    public function __construct(PendingDrawModel $draw)
    {
        $this->draw = $draw;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(Lang::get('SecretSanta - Attente de validation', ['draw' => $this->draw->id]))
            ->markdown('emails.pending_draw', [
                'name' => $this->draw->organizer_name,
                'validationLink' => URL::signedRoute('pending.view', ['pending' => $this->draw->id]).'#'.base64_encode(DrawCrypt::getIV()),
            ]);
    }
}

