<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class InvalidModelStatusException extends ModelNotFoundException
{
}
