<?php

namespace App\Models;

use App\Facades\DrawCrypt;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    public $iv;

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
    public function only($attributes)
    {
        $results = [];

        foreach (is_array($attributes) ? $attributes : func_get_args() as $key => $attribute) {
            $results[is_array($attribute) ? $key : $attribute] = is_array($attribute) ? $this->{$key}->only($attribute) : $this->getAttribute($attribute);
        }

        return $results;
    }
}
