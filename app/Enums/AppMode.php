<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum AppMode: int
{
    use EnumToArray;

    case FREE = 0;
    case OPEN = 1;
    case UNLIMITED = 2;

    public function name(): string
    {
        return trans('app.modes')[$this->value];
    }
}
