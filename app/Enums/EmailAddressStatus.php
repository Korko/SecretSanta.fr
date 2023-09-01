<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum EmailAddressStatus: string
{
    use EnumToArray;

    /**
     * The email was set
     */
    case CREATED = 'created';

    /**
     * The email was confirmed
     */
    case CONFIRMED = 'confirmed';

    /**
     * An error happened during the confirmation
     */
    case ERROR = 'error';

    public function name(): string
    {
        return trans('app.emailAddressStatus')[$this->value];
    }
}
