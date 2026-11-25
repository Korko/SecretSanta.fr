<?php

declare(strict_types=1);

use App\Enums\BounceReason;
use App\Enums\DeliveryStatus;
use App\Events\MailStatusUpdated;
use App\Jobs\ParseBounces;
use App\Models\Draw;
use App\Services\EmailClient;
use App\Services\MailTracker;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Webklex\PHPIMAP\Address;
use Webklex\PHPIMAP\Attribute;
use Webklex\PHPIMAP\Header;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Support\MessageCollection;

use function PHPUnit\Framework\assertEquals;

// Bounces come from the mailer daemon: anything else at the return path says so with $from
function fakeImapMessage(string $returnPath, array $headers = [], string $body = '', string $from = 'MAILER-DAEMON'): Message
{
    $header = Mockery::mock(Header::class);
    $header->shouldReceive('has')->andReturnUsing(fn ($name): bool => array_key_exists($name, $headers));
    $header->shouldReceive('get')->andReturnUsing(fn ($name): Attribute => new Attribute($name, $headers[$name] ?? []));

    $message = Mockery::mock(Message::class);
    $message->shouldReceive('getTo')->andReturn(new Attribute('to', [new Address((object) ['mailbox' => $returnPath])]));
    $message->shouldReceive('getFrom')->andReturn(new Attribute('from', [new Address((object) ['mailbox' => $from])]));
    $message->shouldReceive('getHeader')->andReturn($header);
    $message->shouldReceive('getRawBody')->andReturn($body);

    return $message;
}

// A delivery status notification (RFC 3464) as it comes in the bounce mailbox, the way Postfix writes it
function deliveryReport(string $action, string $status, string $diagnostic = ''): string
{
    return "--b\r\nContent-Type: text/plain\r\n\r\nThis is the mail system.\r\n--b\r\nContent-Type: message/delivery-status\r\n\r\n"
        ."Reporting-MTA: dns; mx.example.com\r\n\r\nFinal-Recipient: rfc822; x@example.com\r\nAction: {$action}\r\n"
        .($status !== '' ? "Status: {$status}\r\n" : '').($diagnostic !== '' ? "Diagnostic-Code: {$diagnostic}\r\n" : '').'--b--';
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
        fakeImapMessage($tracker->getBounceReturnPath($bounced->mail), [], deliveryReport('failed', '5.1.1')),
        fakeImapMessage($tracker->getConfirmReturnPath($confirmed->mail)),
        fakeImapMessage($tracker->getBounceReturnPath($vacation->mail), ['auto_submitted' => 'auto-replied (vacation)']),
    ]));
    $client->shouldReceive('delete', 'trash');
    app()->instance(EmailClient::class, $client);

    app()->call([new ParseBounces, 'handle']);

    assertEquals(DeliveryStatus::Error, $bounced->mail->fresh()->delivery_status);
    assertEquals('5.1.1', $bounced->mail->fresh()->error_code);
    assertEquals(DeliveryStatus::Received, $confirmed->mail->fresh()->delivery_status);
    assertEquals(DeliveryStatus::Received, $vacation->mail->fresh()->delivery_status);
});

it('keeps why an email bounced, from the code of the report only', function (): void {
    config(['mail.return_path' => 'santa-*@example.com']);
    $draw = createServiceDraw(generateParticipants(6));
    // Not the organizer: opening their panel proves their own address works
    [, $unknown, $full, $refused, $oldStyle, $noCode] = $draw->participants->all();
    $tracker = resolve(MailTracker::class);

    $client = Mockery::mock(EmailClient::class);
    $client->shouldReceive('getUnseenMails')->andReturn(MessageCollection::make([
        fakeImapMessage($tracker->getBounceReturnPath($unknown->mail), [], deliveryReport('failed', '5.1.1', 'smtp; 550 5.1.1 <x@example.com>: Recipient address rejected')),
        // No Status field: the code quoted in the diagnostic
        fakeImapMessage($tracker->getBounceReturnPath($full->mail), [], deliveryReport('failed', '', 'smtp; 552 5.2.2 <x@example.com>: Mailbox full')),
        fakeImapMessage($tracker->getBounceReturnPath($refused->mail), [], deliveryReport('failed', '5.7.1', 'smtp; 554 5.7.1 Message rejected as spam')),
        // Not RFC 3464, but from the mailer daemon all the same
        fakeImapMessage($tracker->getBounceReturnPath($oldStyle->mail), [], "Hi. This is the qmail-send program.\r\nI'm afraid I wasn't able to deliver your message.", 'MAILER-DAEMON'),
        fakeImapMessage($tracker->getBounceReturnPath($noCode->mail), [], deliveryReport('failed', '')),
    ]));
    $client->shouldReceive('delete', 'trash');
    app()->instance(EmailClient::class, $client);

    app()->call([new ParseBounces, 'handle']);

    $mail = fn ($participant) => $participant->mail->fresh();
    expect($mail($unknown))->delivery_status->toBe(DeliveryStatus::Error)->error_code->toBe('5.1.1')->bounce_reason->toBe(BounceReason::Address)
        ->and($mail($full))->error_code->toBe('5.2.2')->bounce_reason->toBe(BounceReason::MailboxFull)
        ->and($mail($refused))->error_code->toBe('5.7.1')->bounce_reason->toBe(BounceReason::Refused)
        ->and($mail($oldStyle))->delivery_status->toBe(DeliveryStatus::Error)->error_code->toBeNull()->bounce_reason->toBeNull()
        ->and($mail($noCode))->delivery_status->toBe(DeliveryStatus::Error)->error_code->toBeNull();

    // The organizer panel gets the reason, not the code
    $this->travel(10)->minutes();
    ajaxGet(URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]))
        ->assertSuccessful()
        ->assertJsonPath("participants.{$unknown->hash}.mail.bounce_reason", 'address')
        ->assertJsonPath("participants.{$full->hash}.mail.bounce_reason", 'full')
        ->assertJsonPath("participants.{$oldStyle->hash}.mail.bounce_reason", null)
        ->assertJsonMissingPath("participants.{$unknown->hash}.mail.error_code");

    // Sent again: the old bounce no longer tells anything
    $mail($unknown)->markAsCreated();
    expect($mail($unknown))->error_code->toBeNull()->bounce_reason->toBeNull();
});

it('leaves the status alone when what comes back to the return path is not a bounce', function (): void {
    config(['mail.return_path' => 'santa-*@example.com']);
    $draw = createServiceDraw(generateParticipants(3));
    $participant = $draw->participants->first();
    $tracker = resolve(MailTracker::class);

    // An anti-spam asking the sender to prove they're human, or someone answering the return path
    $client = Mockery::mock(EmailClient::class);
    $client->shouldReceive('getUnseenMails')->andReturn(MessageCollection::make([
        fakeImapMessage($tracker->getBounceReturnPath($participant->mail), [], 'Please click below to confirm you are not a robot.', 'challenge'),
    ]));
    $client->shouldReceive('trash')->once();
    app()->instance(EmailClient::class, $client);

    app()->call([new ParseBounces, 'handle']);

    expect($participant->mail->fresh()->delivery_status)->toBe(DeliveryStatus::Sent);
});

it('reports an unexpected error once and leaves the email aside', function (): void {
    Exceptions::fake();

    $message = Mockery::mock(Message::class);
    $message->shouldReceive('getTo')->andThrow(new RuntimeException('IMAP gone'));
    $message->shouldReceive('getHeader')->andReturn(null);

    $client = Mockery::mock(EmailClient::class);
    $client->shouldReceive('getUnseenMails')->andReturn(MessageCollection::make([$message]));
    // Seen: not fetched again every minute
    $client->shouldReceive('markAsSeen')->once()->with($message);
    $client->shouldNotReceive('delete', 'trash');
    app()->instance(EmailClient::class, $client);

    app()->call([new ParseBounces, 'handle']);

    Exceptions::assertReported(fn (RuntimeException $exception): bool => $exception->getMessage() === 'IMAP gone');
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

it('tells why an email bounced to the organizer panel live and to list-draw', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(3)->create();
    $mail = $draw->participants->first()->mail;
    $mail->markAsError('5.1.1');

    expect(new MailStatusUpdated($mail)->broadcastWith())
        ->toMatchArray(['delivery_status' => DeliveryStatus::Error, 'bounce_reason' => BounceReason::Address])
        ->not->toHaveKey('error_code');

    $url = URL::signedRoute('dearSanta', ['participant' => $draw->participants->last()->hash]).'#'.base64_encode(DrawCrypt::getSecret());
    $this->artisan('secretsanta:list-draw', ['url' => $url])
        ->expectsOutputToContain('error (5.1.1, address)')
        ->assertSuccessful();
});
