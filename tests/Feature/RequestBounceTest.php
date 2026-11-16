<?php

declare(strict_types=1);

use App\Enums\DeliveryStatus;
use App\Jobs\ParseBounces;
use App\Models\Draw;
use App\Services\EmailClient;
use App\Services\MailTracker;
use Webklex\PHPIMAP\Address;
use Webklex\PHPIMAP\Attribute;
use Webklex\PHPIMAP\Header;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Support\MessageCollection;

use function PHPUnit\Framework\assertEquals;

function fakeImapMessage(string $returnPath, array $headers = [], string $body = ''): Message
{
    $header = Mockery::mock(Header::class);
    $header->shouldReceive('has')->andReturnUsing(fn ($name): bool => array_key_exists($name, $headers));
    $header->shouldReceive('get')->andReturnUsing(fn ($name): Attribute => new Attribute($name, $headers[$name] ?? []));

    $message = Mockery::mock(Message::class);
    $message->shouldReceive('getTo')->andReturn(new Attribute('to', [new Address((object) ['mailbox' => $returnPath])]));
    $message->shouldReceive('getHeader')->andReturn($header);
    $message->shouldReceive('getRawBody')->andReturn($body);

    return $message;
}

it('can handle bounced emails', function (): void {
    config(['mail.return_path' => 'santa-*@example.com']);

    ajaxPost('/', [
        'participant-organizer' => true,
        'participants' => generateParticipants(3),
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
    ])->assertSuccessful();

    [$bounced, $confirmed, $vacation] = Draw::query()->find(1)->participants->all();
    $tracker = resolve(MailTracker::class);

    $client = Mockery::mock(EmailClient::class);
    $client->shouldReceive('getUnseenMails')->andReturn(MessageCollection::make([
        fakeImapMessage($tracker->getBounceReturnPath($bounced->mail)),
        fakeImapMessage($tracker->getConfirmReturnPath($confirmed->mail)),
        fakeImapMessage($tracker->getBounceReturnPath($vacation->mail), ['auto_submitted' => 'auto-replied (vacation)']),
    ]));
    $client->shouldReceive('delete', 'trash');
    app()->instance(EmailClient::class, $client);

    app()->call([new ParseBounces, 'handle']);

    assertEquals(DeliveryStatus::Error, $bounced->mail->fresh()->delivery_status);
    assertEquals(DeliveryStatus::Received, $confirmed->mail->fresh()->delivery_status);
    assertEquals(DeliveryStatus::Received, $vacation->mail->fresh()->delivery_status);
});

it('tells a delayed delivery from a bounce', function (): void {
    config(['mail.return_path' => 'santa-*@example.com']);

    ajaxPost('/', [
        'participant-organizer' => true,
        'participants' => generateParticipants(4),
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
    ])->assertSuccessful();

    [$delayed, $failedLater, $received, $bounced] = Draw::query()->find(1)->participants->all();
    $received->mail->markAsReceived();
    $tracker = resolve(MailTracker::class);

    // RFC 3464 delivery status reports, as they come in the bounce mailbox
    $report = fn (string $action): string => "--b\r\nContent-Type: message/delivery-status\r\n\r\nReporting-MTA: dns; mx.example.com\r\n\r\nFinal-Recipient: rfc822; x@example.com\r\nAction: {$action}\r\nStatus: 4.4.7\r\n--b--";

    $client = Mockery::mock(EmailClient::class);
    $client->shouldReceive('getUnseenMails')->andReturn(MessageCollection::make([
        fakeImapMessage($tracker->getBounceReturnPath($delayed->mail), [], $report('delayed')),
        fakeImapMessage($tracker->getBounceReturnPath($failedLater->mail), [], $report('delayed')),
        fakeImapMessage($tracker->getBounceReturnPath($failedLater->mail), [], $report('failed')),
        fakeImapMessage($tracker->getBounceReturnPath($received->mail), [], $report('delayed')),
        fakeImapMessage($tracker->getBounceReturnPath($bounced->mail), [], $report('failed')),
    ]));
    $client->shouldReceive('delete', 'trash');
    app()->instance(EmailClient::class, $client);

    app()->call([new ParseBounces, 'handle']);

    assertEquals(DeliveryStatus::Delayed, $delayed->mail->fresh()->delivery_status);
    assertEquals(DeliveryStatus::Error, $failedLater->mail->fresh()->delivery_status);
    // A late delay report doesn't undo a confirmed reception
    assertEquals(DeliveryStatus::Received, $received->mail->fresh()->delivery_status);
    assertEquals(DeliveryStatus::Error, $bounced->mail->fresh()->delivery_status);
});

