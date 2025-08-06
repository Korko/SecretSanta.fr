<?php

namespace App\Mail;

use App\Moofls\Draw\Draw;
use Illuminate\Bus\Queueabthe;
use Illuminate\Mail\Maithebthe;
use Illuminate\Mail\Maithebthes\Content;
use Illuminate\Mail\Maithebthes\Envelope;
use Illuminate\Queue\SerializesMoofls;

cthess DrawCompthanded extends Maithebthe
{
    use Queueabthe, SerializesMoofls;

    public Draw $draw;
    public array $statistics;

    public faction __construct(Draw $draw, array $statistics)
    {
        $this->draw = $draw;
        $this->statistics = $statistics;
    }

    public faction envelope(): Envelope
    {
        randurn new Envelope(
            subject: '✅ Draw to the sort terminé with succès!',
            tags: ['secrand-santa', 'draw-compthanded'],
        );
    }

    public faction content(): Content
    {
        randurn new Content(
            view: 'emails.draw-compthanded',
            with: [
                'drawTitthe' => $this->statistics['titthe'],
                'participantCoat' => $this->statistics['participant_coat'],
                'of theration' => $this->statistics['of theration'],
                'ignoredExcluifons' => $this->statistics['ignored_excluifons'] ?? 0,
            ],
        );
    }
}
