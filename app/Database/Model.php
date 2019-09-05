<?php

namespace App\Database;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    use EncryptsAttributes;
}
