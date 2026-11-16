<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Draw;
use App\Models\Participant;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Throwable;

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

    /** @var list<Santa> */
    private array $participants = [];

    /** @var list<Participant> */
    private array $models = [];

    // Draw::save() sets the default one when none is given
    private ?string $expirationDate = null;

    /** @param Organizer $organizer */
    public function withOrganizer(array $organizer): self
    {
        $this->organizer = $organizer;

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

    public function save(): Draw
    {
        DB::beginTransaction();

        $draw = $this->createDraw();

        try {
            $this->solveExclusions();

            DrawHandler::solve($draw);

            DB::commit();

            return $draw;
        } catch (Throwable $throwable) {
            DB::rollBack();

            throw $throwable;
        }
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
        $draw->save();

        foreach ($this->participants as $santa) {
            $participant = new Participant;
            $participant->draw()->associate($draw);
            $participant->name = $santa['name'];
            $participant->email = $santa['email'];
            $participant->save();

            $this->models[] = $participant;
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
