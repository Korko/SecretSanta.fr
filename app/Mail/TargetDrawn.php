<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Support\Facades\URL;
use Lang;

class TargetDrawn extends TrackedMailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected readonly Participant $santa
    ) {
    }

    protected function getMailable()
    {
        return $this->santa;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $title = $this->parseKeywords(Lang::get('SecretSanta #:draw - :subject', [
            'draw' => $this->santa->draw->ulid,
            'subject' => $this->santa->draw->title,
        ]), $this->santa);

        $content = $this->parseKeywords($this->santa->draw->description, $this->santa);

        return $this
            ->subject($title)
            ->markdown('emails.target_drawn', [
                'name' => $this->santa->name,
                'content' => $content,
                'dearSantaLink' => URL::hashedRoute('participant.index', ['participant' => $this->santa]),
                'reportLink' => URL::hashedRoute('report.index', ['participant' => $this->santa]),
            ]);
    }

    /**
     * Replace special keywords with participants names
     */
    protected function parseKeywords(string $str, Participant $santa): string
    {
        return str_replace(['{SANTA}', '{TARGET}'], [$santa->name, $santa->target->name], $str);
    }
}
