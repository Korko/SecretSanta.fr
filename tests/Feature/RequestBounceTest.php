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

function fakeImapMessage(string $returnPath, array $headers = []): Message
{
    $header = Mockery::mock(Header::class);
    $header->shouldReceive('has')->andReturnUsing(fn ($name): bool => array_key_exists($name, $headers));
    $header->shouldReceive('get')->andReturnUsing(fn ($name): Attribute => new Attribute($name, $headers[$name] ?? []));

    $message = Mockery::mock(Message::class);
    $message->shouldReceive('getTo')->andReturn(new Attribute('to', [new Address((object) ['mailbox' => $returnPath])]));
    $message->shouldReceive('getHeader')->andReturn($header);

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

it('tracks the organizer recap like the target emails', function (): void {
    config(['mail.return_path' => 'santa-*@example.com']);

    ajaxPost('/', [
        'participant-organizer' => '0',
        'organizer' => ['name' => 'Orga', 'email' => 'orga@example.com'],
        'participants' => generateParticipants(3),
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
    ])->assertSuccessful();

    $draw = Draw::query()->find(1);
    // Sent through the tracked channel, with its own return path
    assertEquals(DeliveryStatus::Sent, $draw->mail->delivery_status);
    expect($draw->organizerReceived())->toBeFalse();

    $client = Mockery::mock(EmailClient::class);
    $client->shouldReceive('getUnseenMails')->andReturn(MessageCollection::make([
        fakeImapMessage(resolve(MailTracker::class)->getConfirmReturnPath($draw->mail)),
    ]));
    $client->shouldReceive('delete', 'trash');
    app()->instance(EmailClient::class, $client);

    app()->call([new ParseBounces, 'handle']);

    assertEquals(DeliveryStatus::Received, $draw->mail->fresh()->delivery_status);
    expect($draw->fresh()->organizerReceived())->toBeTrue();
});
