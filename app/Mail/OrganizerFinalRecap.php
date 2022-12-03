<?php

namespace App\Mail;

use App;
use App\Models\Draw;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Lang;

class OrganizerFinalRecap extends Mailable
{
    protected $draw;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Draw  $draw
     * @return void
     */
    public function __construct(Draw $draw)
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
            ->subject(Lang::get('SecretSanta #:draw - Récapitulatif final organisateur/organisatrice', ['draw' => $this->draw->id]))
            ->markdown('emails.organizer_final_recap', [
                'name' => $this->draw->organizer_name,
                'draw' => $this->draw->id,
            ])
            ->attachData(
                data: app(GenerateDrawCsv::class)->generateFinal($this->draw),
                name: 'secretsanta_' . $this->draw->expires_at->isoFormat('YYYY-MM-DD') . '_final.csv',
                options: ['mime' => 'text/csv'],
            );
    }
}
