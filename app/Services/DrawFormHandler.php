<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Draw;
use App\Models\Participant;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

final class DrawFormHandler
{
    private $title = '';

    private $body = '';

    private ?array $organizer = null;

    private array $participants = [];

    private string $expirationDate;

    public function __construct()
    {
        $this->expirationDate = date('Y-m-d', strtotime('+2 days'));
    }

    public function withOrganizer(array $organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    public function withParticipants(array $participants): self
    {
        $this->participants = $participants;
        $this->organizer ??= reset($participants);

        return $this;
    }

    public function withTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    public function withBody($body): self
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
        $draw->expires_at = $this->expirationDate;
        $draw->mail_title = $this->title;
        $draw->mail_body = $this->body;
        $draw->organizer_name = $this->organizer['name'];
        $draw->organizer_email = $this->organizer['email'];
        $draw->save();

        foreach ($this->participants as $idx => $santa) {
            $participant = new Participant;
            $participant->draw()->associate($draw);
            $participant->name = $santa['name'];
            $participant->email = Arr::get($santa, 'email');
            $participant->save();

            $this->participants[$idx]['model'] = $participant;
            $draw->participants()->save($participant);
        }

        return $draw;
    }

    private function solveExclusions(): void
    {
        $participants = $this->participants;
        for ($i = 0; $i < count($participants); $i++) {
            $participant = $participants[$i];

            $participant['model']->exclusions()->attach(
                array_map(fn ($participantId) => $participants[(int) $participantId]['model']->id, Arr::get($participant, 'exclusions', []))
            );
        }
    }
}
