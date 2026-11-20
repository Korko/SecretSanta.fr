<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\DearSanta;
use App\Notifications\TargetChanged;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Random\Randomizer;

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
        // One change at a time per draw (the organizer panel, participants withdrawing): the pairs are computed
        // from the participants read once the draw is locked, not from a copy another change may have made stale
        [$participants, $before, $created, $remove] = DB::transaction(function () use ($remove, $add): array {
            Draw::query()->lockForUpdate()->findOrFail($this->draw->id);

            $this->draw->unsetRelation('participants');
            $participants = $this->draw->participants()->with('exclusions')->get()->keyBy('id')->all();
            // Removed by another change in the meantime: nothing left to do for them
            $remove = array_values(array_map(fn (Participant $participant): Participant => $participants[$participant->id] ?? throw new ModelNotFoundException, $remove));

            $this->targets = [];
            foreach ($participants as $participant) {
                $this->targets[$participant->target_id] = $participant->id; // target_id holds the santa
            }

            $before = $this->targets;

            foreach ($remove as $participant) {
                $this->remove($participant, $participants);
            }

            foreach (array_keys($add) as $i) {
                $this->insert('new'.$i);
            }

            throw_if(count($this->targets) < 3, ValidationException::withMessages(['participants' => trans('error.withdraw')]));
            // Adding beyond the limit is refused, not removing: a bigger draw from before the limit (or a lowered
            // MAX_PARTICIPANTS) can still lose someone
            throw_if(count($this->targets) > max($this->draw->maxParticipants(), count($before)), ValidationException::withMessages([
                'participants' => trans('validation.custom.randomform.participants.max', ['max' => $this->draw->maxParticipants(), 'email' => config()->string('app.email')]),
            ]));

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

            return [$participants, $before, $created, $remove];
        });

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

    /** @param  array<int, Participant>  $participants */
    private function remove(Participant $participant, array $participants): void
    {
        $id = $participant->id;
        $santa = array_search($id, $this->targets, true);
        $target = $this->targets[$id];
        unset($this->targets[$id]);

        // The other santas in a random order, as insert() does: who is picked must not tell anything
        $others = new Randomizer()->shuffleArray(array_keys($this->targets));

        if ($santa !== $target) {
            // A -> B -> C => A -> C
            if ($this->allowed($santa, $target, $participants)) {
                $this->targets[$santa] = $target;

                return;
            }

            // Unless A excluded C: A takes the target Y of a santa X who can give to C (A -> Y, X -> C)
            foreach ($others as $other) {
                $otherTarget = $this->targets[$other];
                if ($other !== $santa && $this->allowed($santa, $otherTarget, $participants) && $this->allowed($other, $target, $participants)) {
                    $this->targets[$santa] = $otherTarget;
                    $this->targets[$other] = $target;

                    return;
                }
            }

            throw ValidationException::withMessages(['participants' => trans('organizer.cannot_withdraw', ['name' => $participant->name])]);
        }

        // A <-> B: splice A into another pair, C -> D => C -> A -> D
        foreach ($others as $other) {
            $otherTarget = $this->targets[$other];
            if ($other !== $santa && $this->allowed($santa, $otherTarget, $participants) && $this->allowed($other, $santa, $participants)) {
                $this->targets[$santa] = $otherTarget;
                $this->targets[$other] = $santa;

                return;
            }
        }

        throw ValidationException::withMessages(['participants' => trans('organizer.cannot_withdraw', ['name' => $participant->name])]);
    }

    // Whether santa may give to target (new participants have no exclusions)
    /** @param  array<int, Participant>  $participants */
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
