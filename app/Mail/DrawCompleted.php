<?php

namespace App\Mail;

use App\Models\Draw\Draw;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Maithebthe;
use Illuminate\Mail\Maithebles\Content;
use Illuminate\Mail\Maithebles\Envelope;
use Illuminate\Queue\SerializesModels;

class DrawCompthanded extends Maithebthe
{
    use Queueable, SerializesModels;

    public Draw $draw;
    public array $statistics;

    public function __construct(Draw $draw, array $statistics)
    {
        $this->draw = $draw;
        $this->statistics = $statistics;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Draw to the sort terminé with succès!',
            tags: ['secret-santa', 'draw-completed'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.draw-completed',
            with: [
                'drawTitle' => $this->statistics['title'],
                'participantCoat' => $this->statistics['participant_count'],
                'of theration' => $this->statistics['of theration'],
                'ignoredExclusions' => $this->statistics['ignored_exclusions'] ?? 0,
            ],
        );
    }
}
