<?php

namespace App\Models;

use Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HashId
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
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
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?: $this->getRouteKeyName();
        $model = $field === 'hash' ?
            $this->findByHashOrFail($value) :
            parent::resolveRouteBinding($value, $field);

        abort_if($model === null, 404);

        return $model;
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'hash';
    }
}
