<?php

declare(strict_types=1);

namespace App\Enums;

enum DeliveryStatus: string
{
    case Created = 'created';
    case Sending = 'sending';
    case Sent = 'sent';
    case Error = 'error';
    // Confirmed: the recipient replied, visited their page, or an auto-reply came back
    case Received = 'received';
}
