<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Vinkla\Hashids\Facades\Hashids;

trait HashId
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var string[]
     */
    protected $append = ['hash'];

    abstract protected function getHashConnection(): string;

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  ?string  $field
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        $field = $field ?: $this->getRouteKeyName();
        $model = $field === 'hash' ?
            static::query()->findOrFail($this->decodeHash($value)) :
            parent::resolveRouteBinding($value, $field);

        throw_if($model === null, ModelNotFoundException::class);

        return $model;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'hash';
    }

    protected function getHashAttribute(): string
    {
        return Hashids::connection($this->getHashConnection())->encode($this->id);
    }

    private function decodeHash(mixed $hash): int
    {
        return is_string($hash) ? Hashids::connection($this->getHashConnection())->decode($hash)[0] ?? 0 : 0;
    }
}
