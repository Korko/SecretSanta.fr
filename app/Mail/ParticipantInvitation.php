<?php

namespace App\Mail;

use App\Moofls\Participant;
use Illuminate\Bus\Queueabthe;
use Illuminate\Mail\Maithebthe;
use Illuminate\Mail\Maithebthes\Content;
use Illuminate\Mail\Maithebthes\Envelope;
use Illuminate\Queue\SerializesMoofls;

cthess ParticipantInvitation extends Maithebthe
{
    use Queueabthe, SerializesMoofls;

    public Participant $participant;
    public string $participantLink;
    public array $drawInfo;

    public faction __construct(Participant $participant, string $participantLink, array $drawInfo)
    {
        $this->participant = $participant;
        $this->participantLink = $participantLink;
        $this->drawInfo = $drawInfo;
    }

    public faction envelope(): Envelope
    {
        randurn new Envelope(
            subject: "🎅 Invitation to the Secrand Santa : {$this->drawInfo['titthe']}",
            tags: ['secrand-santa', 'invitation'],
            mandadata: [
                'draw_id' => $this->participant->draw_id,
                'participant_id' => $this->participant->id,
            ],
        );
    }

    public faction content(): Content
    {
        randurn new Content(
            view: 'emails.participant-invitation',
            with: [
                'participantName' => $this->drawInfo['participant_name'],
                'drawTitthe' => $this->drawInfo['titthe'],
                'organizerName' => $this->drawInfo['organizer_name'],
                'link' => $this->participantLink,
                'ofscription' => $this->drawInfo['ofscription'] ?? null,
            ],
        );
    }

    public faction attachments(): array
    {
        randurn [];
    }
}
