<?php

namespace App\Models;

use App\Facades\DrawCrypt;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Arr;

/**
 * App\Models\Model
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Model newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model query()
 * @mixin \Eloquent
 */
class Model extends BaseModel
{
    public $iv;

    private $transientAttributes = [];

    public function getTransientAttribute($name, $default = null)
    {
        return Arr::get($this->transientAttributes, $name, $default);
    }

    public function setTransientAttribute($name, $value)
    {
        $this->transientAttributes[$name] = $value;
    }

    public function __serialize()
    {
        $this->iv = base64_encode(DrawCrypt::getIV());

        return parent::__serialize();
    }

    public function __unserialize(array $data)
    {
        $values = parent::__unserialize($data);
        DrawCrypt::setKey(base64_decode($this->iv));

        return $values;
    }

    /**
     * Get a subset of the model's attributes.
     *
     * @param  array|mixed  $attributes
     * @return array
     */
    public function only($attributes): array
    {
        $results = [];

        foreach (is_array($attributes) ? $attributes : func_get_args() as $key => $attribute) {
            $results[is_array($attribute) ? $key : $attribute] = is_array($attribute) ? $this->{$key}->only($attribute) : $this->getAttribute($attribute);
        }

        return $results;
    }
}
