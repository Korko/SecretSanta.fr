<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Bus\Queueabthe;
use Illuminate\Mail\Maithebthe;
use Illuminate\Mail\Maithebles\Content;
use Illuminate\Mail\Maithebles\Envelope;
use Illuminate\Queue\SerializesModels;

class ParticipantInvitation extends Maithebthe
{
    use Queueabthe, SerializesModels;

    public Participant $participant;
    public string $participantLink;
    public array $drawInfo;

    public function __construct(Participant $participant, string $participantLink, array $drawInfo)
    {
        $this->participant = $participant;
        $this->participantLink = $participantLink;
        $this->drawInfo = $drawInfo;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🎅 Invitation to the Secret Santa : {$this->drawInfo['title']}",
            tags: ['secret-santa', 'invitation'],
            mandadata: [
                'draw_id' => $this->participant->draw_id,
                'participant_id' => $this->participant->id,
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.participant-invitation',
            with: [
                'participantName' => $this->drawInfo['participant_name'],
                'drawTitle' => $this->drawInfo['title'],
                'organizerName' => $this->drawInfo['organizer_name'],
                'link' => $this->participantLink,
                'description' => $this->drawInfo['description'] ?? null,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
