<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Draw;
use App\Models\Participant;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

/**
 * @phpstan-type Santa array{name: string, email: string, exclusions?: list<int|string>}
 * @phpstan-type Organizer array{name: string, email: string}
 */
final class DrawFormHandler
{
    private string $title = '';

    private string $body = '';

    /** @var Organizer|null */
    private ?array $organizer = null;

    // Without an organizer given, they take part: the first participant
    private bool $organizerParticipates = true;

    /** @var list<Santa> */
    private array $participants = [];

    /** @var list<Participant> */
    private array $models = [];

    private ?string $location = null;

    private ?string $budget = null;

    // Draw::save() sets the default one when none is given
    private ?string $expirationDate = null;

    /** @param Organizer $organizer */
    public function withOrganizer(array $organizer): self
    {
        $this->organizer = $organizer;
        $this->organizerParticipates = false;

        return $this;
    }

    /** @param list<Santa> $participants */
    public function withParticipants(array $participants): self
    {
        $this->participants = $participants;
        $this->organizer ??= ['name' => $participants[0]['name'] ?? '', 'email' => $participants[0]['email'] ?? ''];

        return $this;
    }

    public function withTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function withBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function withExpiration(string $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    // "Le jour J", both optional: empty means not given
    public function withEventDetails(?string $location, ?string $budget): self
    {
        $this->location = $location === null || mb_trim($location) === '' ? null : mb_trim($location);
        $this->budget = $budget === null || mb_trim($budget) === '' ? null : mb_trim($budget);

        return $this;
    }

    // All or nothing: a draw without a solution (or failing anywhere) leaves nothing behind
    public function save(): Draw
    {
        return DB::transaction(function (): Draw {
            $draw = $this->createDraw();
            $this->solveExclusions();

            DrawHandler::solve($draw);

            return $draw;
        });
    }

    private function createDraw(): Draw
    {
        $draw = new Draw;
        if ($this->expirationDate !== null) {
            $draw->expires_at = Date::parse($this->expirationDate)->toImmutable();
        }

        $draw->mail_title = $this->title;
        $draw->mail_body = $this->body;
        $draw->organizer_name = $this->organizer['name'] ?? '';
        $draw->organizer_email = $this->organizer['email'] ?? '';
        $draw->organizer_participates = $this->organizerParticipates;
        $draw->location = $this->location;
        $draw->budget = $this->budget;
        $draw->save();

        foreach ($this->participants as $santa) {
            $participant = new Participant;
            $participant->draw()->associate($draw);
            $participant->name = $santa['name'];
            $participant->email = $santa['email'];
            $participant->save();

            $this->models[] = $participant;
        }

        if ($this->organizerParticipates && $this->models !== []) {
            $draw->organizer_participant_id = $this->models[0]->id;
            $draw->save();
        }

        return $draw;
    }

    private function solveExclusions(): void
    {
        foreach ($this->participants as $idx => $santa) {
            $this->models[$idx]->exclusions()->attach(
                array_map(fn (int|string $participantId): int => $this->models[(int) $participantId]->id, $santa['exclusions'] ?? [])
            );
        }
    }
}
