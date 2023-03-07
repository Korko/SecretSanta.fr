<?php

namespace App\Mail;

use App;
use App\Facades\DrawCrypt;
use App\Models\Draw;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Lang;

class OrganizerRecap extends Mailable
{
    protected $draw;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Draw $draw)
    {
        $this->draw = $draw;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject(Lang::get('SecretSanta #:draw - Récapitulatif organisateur/organisatrice', ['draw' => $this->draw->id]))
            ->markdown('emails.organizer_recap', [
                'name' => $this->draw->organizer_name,
                'draw' => $this->draw->id,
                'deletionDate' => $this->draw->deletes_at->locale(App::getLocale())->isoFormat('LL'),
                'nextSolvable' => $this->draw->next_solvable,
                'panelLink' => URL::signedRoute('organizer.index', ['draw' => $this->draw->hash]).'#'.base64_encode(DrawCrypt::getIV()),
            ]);
    }
}
