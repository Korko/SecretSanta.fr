<?php

declare(strict_types=1);

use App\Enums\BounceReason;

it('tells what the organizer can do from the code of a bounce', function (?string $code, ?BounceReason $reason): void {
    expect(BounceReason::fromCode($code))->toBe($reason);
})->with([
    'no such mailbox' => ['5.1.1', BounceReason::Address],
    'no such domain' => ['5.1.2', BounceReason::Address],
    'domain not found' => ['5.4.4', BounceReason::Address],
    'mailbox disabled' => ['5.2.1', BounceReason::Address],
    'mailbox full' => ['5.2.2', BounceReason::MailboxFull],
    'given up after a full mailbox' => ['4.2.2', BounceReason::MailboxFull],
    'refused as spam' => ['5.7.1', BounceReason::Refused],
    'our own sender address' => ['5.1.8', null],
    'too big' => ['5.3.4', null],
    'a success' => ['2.0.0', null],
    'not a code' => ['550', null],
    'none' => [null, null],
]);
