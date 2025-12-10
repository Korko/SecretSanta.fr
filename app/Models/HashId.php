<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HashId
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var string[]
     */
    protected $append = ['hash'];

    protected function getHashConnection()
    {
        return property_exists($this, 'hashConnection') ? $this->hashConnection : null;
    }

    public function getHashAttribute()
    {
        return Hashids::connection($this->getHashConnection())->encode($this->id);
    }

    public function scopeFindByHashOrFail($query, $hash)
    {
        $ids = Hashids::connection($this->getHashConnection())->decode($hash);

        return $query->findOrFail(collect((array) $ids)->first());
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     */
    public function resolveRouteBinding($value, ?string $field = null): ?Model
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
}
