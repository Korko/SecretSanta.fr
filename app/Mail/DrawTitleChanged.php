<?php

namespace App\Mail;

use App\Models\Draw;
use Illuminate\Mail\Mailable;
use Lang;

class DrawTitleChanged extends Mailable
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
            ->subject(Lang::get('SecretSanta #:draw - Titre changé', ['draw' => $this->draw->ulid]));
            //TODO
    }
}
