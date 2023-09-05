<?php

use App\Models\Draw;
use App\Services\DrawFormHandler;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\TestResponse;
use Tests\DuskTestCase;
use Tests\TestCase;

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

uses(DuskTestCase::class)->in('Browser');
uses(TestCase::class)->in('Feature');

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

/**
 * Assert the count of model entries.
 *
 * @param  string  $class
 * @param  int  $count
 * @return void
 */
expect()->intercept('toHaveCount', fn (mixed $value) => is_string($value) && is_subclass_of($value, Model::class), function (int $count) {
    return test()->assertDatabaseCount($this->value, $count);
});

expect()->extend('toExists', function () {
    return test()->assertModelExists($this->value);
});

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

function prepareAjax($headers = []): TestCase
{
    $headers = $headers + [
        'Accept' => 'application/json',
        'X-Requested-With' => 'XMLHttpRequest',
        'X-HASH-IV' => base64_encode(DrawCrypt::getIV()),
    ];

    return test()->withHeaders($headers);
}

function ajaxPost($url, array $postArgs = [], $headers = []): TestResponse
{
    return prepareAjax($headers)->json('POST', $url, $postArgs);
}

function ajaxGet($url, $headers = []): TestResponse
{
    return prepareAjax($headers)->json('GET', $url);
}

function ajaxDelete($url, $headers = []): TestResponse
{
    return prepareAjax($headers)->json('DELETE', $url);
}

function createDraw($participants, $params = [])
{
    return ajaxPost(URL::route('form.process'), $params + [
        'participant-organizer' => '1',
        'participants' => $participants,
        'title' => 'this is a test',
        'content' => 'test mail {SANTA} => {TARGET}',
    ])
        ->assertJsonStructure(['message']);
}
/*
function createServiceDraw($participants, $data = []): Draw
{
    $pendingDraw = PendingDraw::factory()
        ->state(function (array $attributes) use ($participants, $data) {
            return [
                'data' => [
                    'participants' => $participants,
                ] + $data + $attributes['data'],
            ];
        })
        ->create();

    return (new DrawFormHandler())->handle($pendingDraw);
}
*/
/**
 * Laravel TestCase aliases
 */

/**
 * @see \Illuminate\Foundation\Testing\Concerns\InteractsWithConsole
 */
function artisan($command, $parameters = [])
{
    return test()->artisan(...func_get_args());
}

/**
 * @see \Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase
 */
function seed($class = 'Database\\Seeders\\DatabaseSeeder')
{
    return test()->seed(...func_get_args());
}
