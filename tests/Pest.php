<?php

use App\Jobs\ParseBounces;
use App\Mail\TargetDrawn;
use App\Models\Draw;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use App\Services\EmailClient;
use function Pest\Faker\faker;
use PHPUnit\Framework\TestCase;
use Illuminate\Testing\TestResponse;
use Webklex\PHPIMAP\Message as EmailMessage;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(Tests\DuskTestCase::class)->in('Browser');
uses(Tests\TestCase::class)->in('Feature');
uses(Illuminate\Foundation\Testing\DatabaseMigrations::class, Illuminate\Foundation\Testing\DatabaseTransactions::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

/*expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function assertHasMailPushed($class, $recipient = null, Closure $callback = null) : void {
    Mail::assertSent($class, function ($mail) use ($recipient, $callback) {
        if ($recipient === null || $mail->hasTo($recipient)) {
            if ($callback !== null) {
                $callback($mail);
            }

            return true;
        }

        return false;
    });
}

function prepareAjax($headers = []) : TestCase {
    $headers = $headers + [
        'Accept'           => 'application/json',
        'X-Requested-With' => 'XMLHttpRequest',
        'X-HASH-KEY'       => base64_encode(DrawCrypt::getKey())
    ];
    return test()->withHeaders($headers);
}

function ajaxPost($url, array $postArgs = [], $headers = []) : TestResponse {
    return prepareAjax($headers)->json('POST', $url, $postArgs);
}

function ajaxGet($url, $headers = []) : TestResponse {
    return prepareAjax($headers)->json('GET', $url);
}

function ajaxDelete($url, $headers = []) : TestResponse {
    return prepareAjax($headers)->json('DELETE', $url);
}

function createServiceDraw($participants) : Draw {
    return DrawFormHandler::withParticipants($participants)
        ->withExpiration(date('Y-m-d', strtotime('+2 days')))
        ->withTitle('test mail {SANTA} => {TARGET} title')
        ->withBody('test mail {SANTA} => {TARGET} body')
        ->save();
}

function generateParticipants(int $totalParticipants) : array {
    $participants = [];
    for ($i = 0; $i < $totalParticipants; $i++) {
        $participants[] = [
            'name' => faker()->unique()->name,
            'email' => faker()->unique()->safeEmail,
            'target' => ($i === 0) ? $totalParticipants - 1 : $i - 1
        ];
    }

    return formatParticipants($participants);
}

/**
 * Expected $participants array format:
 *
 * $participants = [
 *  [
 *      'name'   => 'foo',
 *      'email'  => 'test@test.com',
 *      'target' => 1,
 *  ],
 *  [
 *      'name'   => 'bar',
 *      'email'  => 'test2@test.com',
 *      'target' => 2,
 *  ],
 *  [
 *      'name'   => 'foobar',
 *      'email'  => 'test3@test.com',
 *      'target' => 0,
 *  ],
 * ];
 */
function formatParticipants($participants) : array {
    $participants = array_map(function ($idx) use ($participants) {
        if (isset($participants[$idx]['target'])) {
            $participants[$idx] += [
                // Remove the keys and cast as string to simulate an html form submission
                'exclusions' => array_values(
                    array_map('strval',
                        // Get all the participants idx but the current one and the target
                        // (this participant will only draw their target and nobody else)
                        array_diff(array_keys($participants), [$idx], [$participants[$idx]['target']])
                    )
                ),
            ];
        }
        return $participants[$idx];
    }, array_keys($participants));

    return $participants;
}

function assertReturnPath($prop, $status) : void {
    Mail::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    // Fake emails as bounces
    $emailClient = test()->mock(EmailClient::class, function ($mock) use ($draw, $prop) {
        // Get all the return path defined by the app
        $links = [];
        Mail::assertSent(function (TargetDrawn $mail) use (&$links, $prop) {
            $links[] = $mail->$prop;

            return true;
        });
        test()->assertEquals(count($draw->participants), count($links));

        // Fake the list of "unseen mails"
        $messages = [];
        foreach($links as $link) {
            $messages[] = test()->mock(EmailMessage::class, function ($mock) use ($link) {
                $mock
                    ->shouldReceive('getTo')
                    ->once()
                    ->andReturn([
                        (object) ['mailbox' => $link]
                    ]);

                $mock->shouldReceive('move')->once();
            });
        }

        $mock
            ->shouldReceive('getUnseenMails')
            ->once()
            ->andReturn($messages);
    });

    // Ensure all participants are marked as bounced
    foreach ($draw->participants as $participant) {
        test()->assertEquals(MailModel::CREATED, $participant->mail->delivery_status);
    }

    // Parse bounces and mark them as bounced
    $job = new ParseBounces();
    $job->handle($emailClient);

    // Ensure all participants are marked as bounced
    foreach ($draw->participants as $participant) {
        // Get the last delivery_status from DB via the "fresh" method
        test()->assertEquals($status, $participant->mail->fresh()->delivery_status);
    }
}