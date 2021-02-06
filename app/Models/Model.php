<?php

namespace App\Models;

use DrawCrypt;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    public $key;

    public function __serialize()
    {
        $this->key = base64_encode(DrawCrypt::getKey());
        return parent::__serialize();
    }

    public function __unserialize(array $data)
    {
        $values = parent::__unserialize($data);
        DrawCrypt::setKey(base64_decode($this->key));
        return $values;
    }
}
