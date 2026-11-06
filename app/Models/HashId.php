<?php

declare(strict_types=1);

namespace App\Models;

use Hashids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HashId
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var string[]
     */
    protected $append = ['hash'];

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
            $this->findByHashOrFail($value) :
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

    protected function getHashAttribute()
    {
        return Hashids::connection($this->getHashConnection())->encode($this->id);
    }

    protected function scopeFindByHashOrFail($query, $hash)
    {
        $ids = Hashids::connection($this->getHashConnection())->decode($hash);

        return $query->findOrFail(collect((array) $ids)->first());
    }

    protected function getHashConnection()
    {
        return property_exists($this, 'hashConnection') ? $this->hashConnection : null;
    }
}
