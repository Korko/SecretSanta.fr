<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TrackingState;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

/**
 * Follows a draw from the browser that sent its form (#117)
 *
 * @property int $id
 * @property string $uuid
 * @property string $token_hash
 * @property TrackingState $state
 * @property string|null $failure_reason
 * @property int|null $draw_id
 * @property-read Draw|null $draw
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
final class DrawTracking extends Model
{
    use Prunable;

    public const string REASON_NO_SOLUTION = 'no_solution';

    public const string REASON_LIMIT = 'limit';

    public const string REASON_ERROR = 'error';

    /**
     * A new pending tracking and its token: only the hash of the token is stored
     *
     * @return array{self, string}
     */
    public static function start(): array
    {
        $token = Str::random(40);

        $tracking = new self;
        $tracking->uuid = Str::uuid()->toString();
        $tracking->token_hash = self::hash($token);
        $tracking->state = TrackingState::Pending;
        $tracking->save();

        return [$tracking, $token];
    }

    public function checkToken(?string $token): bool
    {
        return is_string($token) && $token !== '' && hash_equals($this->token_hash, self::hash($token));
    }

    /** @return BelongsTo<Draw, $this> */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    // Private channel (see BroadcastAuthController::tracking), named after a secret so the shared UUID isn't enough
    public function broadcastChannel(): string
    {
        return 'tracking.'.hash_hmac('sha256', 'tracking.'.$this->id, config()->string('app.key'));
    }

    /**
     * What the browser that sent the form may know: the participants' hashes in the form order
     *
     * @return array{state: string, reason?: string|null, participants?: list<string>}
     */
    public function details(): array
    {
        return match ($this->state) {
            TrackingState::Pending => ['state' => $this->state->value],
            TrackingState::Failed => ['state' => $this->state->value, 'reason' => $this->failure_reason],
            TrackingState::Drawn => [
                'state' => $this->state->value,
                'participants' => array_values($this->draw?->participants->sortBy('id')->map(fn (Participant $participant): string => $participant->hash)->all() ?? []),
            ],
        };
    }

    // A failed or stuck draw: the browser has the form. A drawn one goes with its draw
    /** @return Builder<self> */
    public function prunable(): Builder
    {
        return self::query()->whereNull('draw_id')->where('created_at', '<', Date::now()->subDay());
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'state' => TrackingState::class,
        ];
    }

    private static function hash(string $token): string
    {
        return hash('sha256', $token);
    }
}
