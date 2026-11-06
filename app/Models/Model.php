<?php

declare(strict_types=1);

namespace App\Models;

use DrawCrypt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{
    use HasFactory;

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
}
