<?php

namespace App\Database;

use Illuminate\Database\Eloquent\Model as BaseModel;
use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;

class Model extends BaseModel
{
    use HasEncryptedAttributes;
}
