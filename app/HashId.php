<?php

namespace App;

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

    protected static function getHashConnection()
    {
        return property_exists(new static, 'hashConnection') ? static::$hashConnection : null;
    }

    public function getHashAttribute()
    {
        return Hashids::connection($this->getHashConnection())->encode($this->id);
    }

    public static function findByHashOrFail($hash)
    {
        if (! $hash || ! ($ids = Hashids::connection(static::getHashConnection())->decode($hash))) {
            throw (new ModelNotFoundException())->setModel(__CLASS__);
        }

        return static::findOrFail($ids[0]);
    }
}
