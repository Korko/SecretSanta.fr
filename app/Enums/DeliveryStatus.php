<?php

declare(strict_types=1);

namespace App\Enums;

enum DeliveryStatus: string
{
    case Created = 'created';
    case Sending = 'sending';
    case Sent = 'sent';
    case Error = 'error';
    // A server reported a delay and keeps trying (RFC 3464 "Action: delayed"): not an error yet
    case Delayed = 'delayed';
    // Confirmed: the recipient replied, visited their page, or an auto-reply came back
    case Received = 'received';

    // What the sender of a message or a gift idea may know: whether it left, not whether it was read or bounced
    public function isVisibleToSender(): bool
    {
        return in_array($this, [self::Created, self::Sending, self::Sent], true);
    }
}
