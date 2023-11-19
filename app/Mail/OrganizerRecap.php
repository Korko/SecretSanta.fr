<?php

namespace App\Mail;

use App;
use App\Actions\GenerateDrawCsv;
use App\Models\Draw;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Lang;

class OrganizerRecap extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected readonly Draw $draw
    ) {
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject(Lang::get('SecretSanta #:draw - Récapitulatif organisateur/organisatrice', ['draw' => $this->draw->ulid]))
            ->markdown('emails.organizer_recap', [
                'name' => $this->draw->organizer->name,
                'draw' => $this->draw->ulid,
                'deletionDate' => $this->draw->deletes_at->locale(App::getLocale())->isoFormat('LL'),
                'panelLink' => URL::hashedSignedRoute('organizer.index', ['draw' => $this->draw]),
            ])
            ->attachData(
                data: app(GenerateDrawCsv::class)->generateInitial($this->draw),
                name: 'secretsanta_'.$this->draw->expires_at->isoFormat('YYYY-MM-DD').'_init.csv',
                options: ['mime' => 'text/csv'],
            );
    }
}
