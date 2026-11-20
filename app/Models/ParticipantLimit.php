<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

/**
 * A single use code from the administrator: the draw created with it may go up to $max participants.
 *
 * @property int $id
 * @property string $code_hash
 * @property int $max
 * @property CarbonImmutable|null $used_at
 * @property int|null $draw_id
 * @property CarbonImmutable|null $created_at
 */
final class ParticipantLimit extends Model
{
    use Prunable;

    /** Creates a limit and returns its code: only its hash is stored */
    public static function generate(int $max): string
    {
        $code = Str::random(32);

        $limit = new self;
        $limit->code_hash = self::hash($code);
        $limit->max = $max;
        $limit->save();

        return $code;
    }

    public static function findUsable(?string $code): ?self
    {
        if ($code === null || $code === '') {
            return null;
        }

        return self::query()->where('code_hash', self::hash($code))->whereNull('used_at')->first();
    }

    /** Atomic: false when another draw used the code in the meantime */
    public function consume(Draw $draw): bool
    {
        return self::query()->whereKey($this->id)->whereNull('used_at')
            ->update(['used_at' => Date::now(), 'draw_id' => $draw->id]) === 1;
    }

    /** @return Builder<self> */
    public function prunable(): Builder
    {
        return self::query()->where('created_at', '<', Date::now()->subYear());
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'max' => 'integer',
            'used_at' => 'immutable_datetime',
        ];
    }

    private static function hash(string $code): string
    {
        return hash('sha256', $code);
    }
}
