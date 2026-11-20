<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\DearSanta;
use App\Notifications\TargetChanged;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use LogicException;

/**
 * Adds and removes participants of an existing draw in one go, touching as few pairs as possible,
 * then sends a single email to each santa whose target changed.
 */
final class ParticipantsEditor
{
    /** @var array<int|string, int|string> santa id => target id (new participants have string keys) */
    private array $targets = [];

    public function __construct(private readonly Draw $draw) {}

    /**
     * @param  array<Participant>  $remove
     * @param  list<array{name: string, email: string}>  $add
     */
    public function apply(array $remove, array $add): void
    {
        // One change at a time per draw: a change computed from pairs that another one is modifying (two withdrawals,
        // two tabs of the panel) would leave a pair pointing to a removed participant
        /** @var array{array<Participant>, array<int|string, int|string>, list<Participant>, array<string, Participant>} $changed */
        $changed = DB::transaction(function () use ($remove, $add): array {
            Draw::query()->whereKey($this->draw->id)->lockForUpdate()->firstOrFail();
            $this->draw->unsetRelation('participants');

            return $this->change($remove, $add);
        });
        [$participants, $before, $remove, $created] = $changed;

        $this->draw->unsetRelation('participants');

        // The final CSV (next year's exclusions) depends on the current pairs: computed on a fresh copy,
        // canRedraw() adds each target to the loaded exclusions
        $this->draw->next_solvable = DrawHandler::canRedraw($this->draw->participants()->with(['exclusions', 'target'])->get());
        $this->draw->save();

        $removed = array_map(fn (Participant $participant): int => $participant->id, $remove);
        foreach ($this->targets as $santa => $target) {
            if (isset($created[$santa])) {
                $created[$santa]->notify(new TargetDrawn);
            } elseif ($before[$santa] !== $target) {
                $santa = $participants[$santa]->refresh()->load('target.dearSantas');
                $santa->notify(in_array($before[$santa->id], $removed, true) ? new TargetWithdrawn : new TargetChanged);
                // Messages the new target wrote to their santa now belong to this one
                $santa->target->dearSantas->each(fn ($dearSanta) => $santa->notify(new DearSanta($dearSanta)));
            }
        }
    }

    /**
     * Computes the new pairs from the current ones, then saves them
     *
     * @param  array<Participant>  $remove
     * @param  list<array{name: string, email: string}>  $add
     * @return array{array<Participant>, array<int|string, int|string>, list<Participant>, array<string, Participant>}
     */
    private function change(array $remove, array $add): array
    {
        $participants = $this->draw->participants->load('exclusions')->keyBy('id')->all();
        foreach ($participants as $participant) {
            $this->targets[$participant->target_id] = $participant->id; // target_id holds the santa
        }

        $before = $this->targets;

        // Already removed by another change of the draw: nothing left to do for them
        $remove = array_values(array_filter($remove, fn (Participant $participant): bool => isset($participants[$participant->id])));
        foreach ($remove as $participant) {
            $this->remove($participant, $participants);
        }

        foreach (array_keys($add) as $i) {
            $this->insert('new'.$i);
        }

        throw_if(count($this->targets) < 3, ValidationException::withMessages(['participants' => trans('error.withdraw')]));

        $created = [];
        foreach ($add as $i => $data) {
            $participant = new Participant(['name' => $data['name'], 'email' => $data['email']]);
            $participant->draw()->associate($this->draw)->save();
            $created['new'.$i] = $participant;
        }

        foreach ($remove as $participant) {
            $participant->delete();
        }

        $model = fn (int|string $id): Participant => $created[$id] ?? $participants[$id];
        foreach ($this->targets as $santa => $target) {
            $model($target)->santa()->associate($model($santa))->save();
        }

        return [$participants, $before, $remove, $created];
    }

    /** @param  array<Participant>  $participants */
    private function remove(Participant $participant, array $participants): void
    {
        $id = $participant->id;
        $santa = array_search($id, $this->targets, true);
        throw_if($santa === false, LogicException::class, 'The participant is not in the loop');
        $target = $this->targets[$id];
        unset($this->targets[$id]);

        if ($santa !== $target && $this->allowed($santa, $target, $participants)) {
            // A -> B -> C => A -> C
            $this->targets[$santa] = $target;

            return;
        }

        // A excluded C, or A <-> B (then C is A): A takes the target Y of another santa X, and X takes C.
        // A -> B -> C, X -> Y => A -> Y, X -> C; A <-> B, X -> Y => X -> A -> Y.
        // X is drawn at random, like in insert(): which santa changed must not be guessable
        foreach (Arr::shuffle(array_keys($this->targets)) as $other) {
            $otherTarget = $this->targets[$other];
            if ($other !== $santa && $this->allowed($santa, $otherTarget, $participants) && $this->allowed($other, $target, $participants)) {
                $this->targets[$santa] = $otherTarget;
                $this->targets[$other] = $target;

                return;
            }
        }

        throw ValidationException::withMessages(['participants' => trans('organizer.cannot_withdraw', ['name' => $participant->name])]);
    }

    // Nobody draws themselves or someone they excluded (new participants have no exclusions yet)
    /** @param  array<Participant>  $participants */
    private function allowed(int|string $santa, int|string $target, array $participants): bool
    {
        return $santa !== $target
            && (! isset($participants[$santa]) || $participants[$santa]->exclusions->doesntContain('id', $target));
    }

    // A -> B => A -> new -> B, with a random A so the new participant's santa can't be guessed
    private function insert(string $id): void
    {
        $santas = array_keys($this->targets);
        $santa = $santas[random_int(0, count($santas) - 1)];
        $this->targets[$id] = $this->targets[$santa];
        $this->targets[$santa] = $id;
    }
}
