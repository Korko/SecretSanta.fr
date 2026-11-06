<?php

declare(strict_types=1);

use App\Jobs\ParseBounces;
use App\Models\Draw;
use App\Models\Mail as MailModel;
use App\Services\EmailClient;
use App\Services\MailTracker;
use Webklex\PHPIMAP\Attribute;
use Webklex\PHPIMAP\Header;
use Webklex\PHPIMAP\Message;

use function PHPUnit\Framework\assertEquals;

function fakeImapMessage(string $returnPath, array $headers = []): Message
{
    $header = Mockery::mock(Header::class);
    $header->shouldReceive('has')->andReturnUsing(fn ($name): bool => array_key_exists($name, $headers));
    $header->shouldReceive('get')->andReturnUsing(fn ($name): Attribute => new Attribute($name, $headers[$name] ?? []));

    $message = Mockery::mock(Message::class);
    $message->shouldReceive('getTo')->andReturn([(object) ['mailbox' => $returnPath]]);
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
    $client->shouldReceive('getUnseenMails')->andReturn([
        fakeImapMessage($tracker->getBounceReturnPath($bounced->mail)),
        fakeImapMessage($tracker->getConfirmReturnPath($confirmed->mail)),
        fakeImapMessage($tracker->getBounceReturnPath($vacation->mail), ['auto_submitted' => 'auto-replied (vacation)']),
    ]);
    $client->shouldReceive('delete', 'trash');
    app()->instance(EmailClient::class, $client);

    app()->call([new ParseBounces, 'handle']);

    assertEquals(MailModel::ERROR, $bounced->mail->fresh()->delivery_status);
    assertEquals(MailModel::RECEIVED, $confirmed->mail->fresh()->delivery_status);
    assertEquals(MailModel::RECEIVED, $vacation->mail->fresh()->delivery_status);
});
