<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum PendingDrawStatus: string
{
    use EnumToArray;

    /**
     * The draw was created but is not yet validated
     */
    case CREATED = 'created';

    /**
     * The draw was validated and is ready to be processed
     */
    case READY = 'ready';

    /**
     * The draw is processing
     */
    case DRAWING = 'drawing';

    /**
     * The draw is fully processed and the participants can use the website
     */
    case STARTED = 'started';

    /**
     * The draw is unsolvable and thus, cannot be processed
     */
    case ERROR = 'error';

    /**
     * The draw was canceled by the organizer
     */
    case CANCELED = 'canceled';

    public function name(): string
    {
        return trans('app.pendingDrawStatus')[$this->value];
    }
}
