<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\TrackingState;
use App\Events\DrawTrackingUpdated;
use App\Exceptions\SolverException;
use App\Models\Draw;
use App\Models\DrawTracking;
use App\Models\ParticipantLimit;
use App\Notifications\OrganizerRecap;
use App\Services\DrawFormHandler;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Support\Facades\DB;
use STS\Metrics\Facades\Metrics;
use Throwable;

/**
 * Solves a draw out of the form request (#117): until then, the form is only in this job's encrypted payload,
 * nothing in the database. Without a solution, only the tracking is kept, failed, with a reason
 *
 * @phpstan-type Form array{participant-organizer?: bool, organizer?: array{name: string, email: string}, participants: list<array{name: string, email: string, exclusions?: list<int|string>}>, title: string, content-email: string, data-expiration: string, data-location?: string|null, data-budget?: string|int|float|null}
 */
#[Tries(2)]
final class SolveDraw implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    public int $timeout;

    /** @param Form $form */
    public function __construct(public int $trackingId, public array $form, public ?int $limitId = null)
    {
        $this->timeout = config()->integer('app.solve_timeout');
    }

    public function handle(): void
    {
        try {
            $draw = DB::transaction(function (): ?Draw {
                // Locked: a job run again (timeout, retry) must not make a second draw
                $tracking = DrawTracking::query()->lockForUpdate()->find($this->trackingId);
                if (! $tracking instanceof DrawTracking || $tracking->state !== TrackingState::Pending) {
                    return null;
                }

                $draw = $this->handler()->save();

                // Single use: the draw keeps the raised limit for the participants added later
                if ($this->limitId !== null) {
                    $limit = ParticipantLimit::query()->find($this->limitId);
                    throw_if(! $limit instanceof ParticipantLimit || ! $limit->consume($draw), LimitUsedException::class);

                    $draw->max_participants = $limit->max;
                    $draw->save();
                }

                $tracking->state = TrackingState::Drawn;
                $tracking->draw()->associate($draw);
                $tracking->save();

                event(new DrawTrackingUpdated($tracking));

                return $draw;
            });
        } catch (SolverException) {
            Metrics::create('solver_failed')
                ->addExtra('participants', count($this->form['participants']));
            $this->fail(DrawTracking::REASON_NO_SOLUTION);

            return;
        } catch (LimitUsedException) {
            $this->fail(DrawTracking::REASON_LIMIT);

            return;
        }

        if (! $draw instanceof Draw) {
            return;
        }

        // Taking part, the organizer gets the recap within their own target email (TargetDrawn)
        if (! ($this->form['participant-organizer'] ?? false)) {
            $draw->notify(new OrganizerRecap($draw));
        }

        $draw->createMetric('new_draw')
            ->addExtra('participants', count($draw->participants));
    }

    // Out of tries (or timed out): the browser shows the form again
    public function failed(?Throwable $exception = null): void
    {
        $this->fail(DrawTracking::REASON_ERROR);
    }

    private function fail(string $reason): void
    {
        $tracking = DrawTracking::query()->find($this->trackingId);
        if (! $tracking instanceof DrawTracking || $tracking->state !== TrackingState::Pending) {
            return;
        }

        $tracking->state = TrackingState::Failed;
        $tracking->failure_reason = $reason;
        $tracking->save();

        event(new DrawTrackingUpdated($tracking));
    }

    private function handler(): DrawFormHandler
    {
        $handler = new DrawFormHandler;

        if (! ($this->form['participant-organizer'] ?? false) && isset($this->form['organizer'])) {
            $handler->withOrganizer($this->form['organizer']);
        }

        return $handler
            ->withParticipants($this->form['participants'])
            ->withTitle($this->form['title'])
            ->withBody($this->form['content-email'])
            ->withExpiration($this->form['data-expiration'])
            ->withEventDetails($this->form['data-location'] ?? null, isset($this->form['data-budget']) ? (string) $this->form['data-budget'] : null);
    }
}
