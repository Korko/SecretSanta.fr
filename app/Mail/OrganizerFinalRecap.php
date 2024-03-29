<?php

namespace App\Mail;

use App\Models\Draw;
use Illuminate\Mail\Mailable;
use Lang;

class OrganizerFinalRecap extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Draw  $draw
     * @return void
     */
    public function __construct(
        protected readonly Draw $draw
    ) {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(Lang::get('SecretSanta #:draw - Récapitulatif final organisateur/organisatrice', ['draw' => $this->draw->ulid]))
            ->markdown('emails.organizer_final_recap', [
                'name' => $this->draw->organizer->name,
                'draw' => $this->draw->ulid,
            ])
            ->attachData(
                data: app(GenerateDrawCsv::class)->generateFinal($this->draw),
                name: 'secretsanta_'.$this->draw->expires_at->isoFormat('YYYY-MM-DD').'_final.csv',
                options: ['mime' => 'text/csv'],
            );
    }
}
