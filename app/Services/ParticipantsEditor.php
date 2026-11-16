<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\DearSanta;
use App\Notifications\TargetChanged;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        $participants = $this->draw->participants->load('exclusions')->keyBy('id')->all();
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

        $created = DB::transaction(function () use ($remove, $add, $participants): array {
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

            return $created;
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

        if ($santa !== $target) {
            // A -> B -> C => A -> C
            $this->targets[$santa] = $target;

            return;
        }

        // A <-> B: splice A into another pair, C -> D => C -> A -> D
        $excluded = isset($participants[$santa]) ? $participants[$santa]->exclusions->modelKeys() : [];
        foreach ($this->targets as $other => $otherTarget) {
            if ($other !== $santa && ! in_array($otherTarget, $excluded, true)
                && (! isset($participants[$other]) || $participants[$other]->exclusions->doesntContain('id', $santa))) {
                $this->targets[$santa] = $otherTarget;
                $this->targets[$other] = $santa;

                return;
            }
        }

        throw ValidationException::withMessages(['participants' => trans('organizer.cannot_withdraw', ['name' => $participant->name])]);
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
