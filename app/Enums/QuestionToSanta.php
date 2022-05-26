<?php

namespace App\Enums;

enum QuestionToSanta : string
{
    case PRESENTATION = 'presentation';
    case IDEAS = 'ideas';

    public function body(): string {
        return trans('app.santa')[$this->value];
    }
}
