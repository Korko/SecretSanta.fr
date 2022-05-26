<?php

namespace App\Enums;

enum AppMode : int
{
    case FREE = 0;
    case OPEN = 1;
    case UNLIMITED = 2;

    public function name(): string {
        return trans('app.modes')[$this->value];
    }
}
