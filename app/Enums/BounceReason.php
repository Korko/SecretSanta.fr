<?php

declare(strict_types=1);

namespace App\Enums;

// What the organizer can do about a bounce, from its enhanced status code (RFC 3463 class.subject.detail)
enum BounceReason: string
{
    // No such mailbox or domain, or a disabled one: the address needs fixing
    case Address = 'address';
    // The mailbox is full: sending again later may work
    case MailboxFull = 'full';
    // The recipient's server refused the mail (policy, spam filter): the address isn't the problem
    case Refused = 'refused';

    public static function fromCode(?string $code): ?self
    {
        if ($code === null || preg_match('/^[45]\.(\d{1,3})\.(\d{1,3})$/', $code, $matches) !== 1) {
            return null;
        }

        [$subject, $detail] = [(int) $matches[1], (int) $matches[2]];

        return match (true) {
            // X.1.7 and X.1.8 are about the sender's address, not the recipient's
            $subject === 1 && in_array($detail, [1, 2, 3, 6, 10], true),
            $subject === 2 && $detail === 1, // mailbox disabled
            $subject === 4 && $detail === 4 => self::Address, // domain not found ("gmial.com")
            $subject === 2 && $detail === 2 => self::MailboxFull,
            $subject === 7 => self::Refused,
            default => null,
        };
    }
}
