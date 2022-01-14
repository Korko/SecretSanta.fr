<?php

use PHPUnit\Framework\TestCase;
use Illuminate\Testing\TestResponse;

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

/**
 * Assert the count of model entries.
 *
 * @param  string  $class
 * @param  int  $count
 * @return $this
 */
function assertModelCount($class, int $count) {
    return test()->assertDatabaseCount(
        test()->getTable($class),
        $count
    );
}

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

function prepareAjax($headers = []) : TestCase {
    $headers = $headers + [
        'Accept'           => 'application/json',
        'X-Requested-With' => 'XMLHttpRequest',
        'X-HASH-IV'       => base64_encode(DrawCrypt::getIV())
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

/**
 * Laravel TestCase aliases
 */

/**
 * @see \Illuminate\Foundation\Testing\Concerns\InteractsWithConsole
 */
function artisan($command, $parameters = []) {
    return test()->artisan($command, $parameters);
}

/**
 * @see \Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase
 */
function seed($class = 'Database\\Seeders\\DatabaseSeeder') {
    return test()->seed($class);
}

/**
 * @see \Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase
 */
function assertModelExists($model) {
    return test()->assertModelExists($model);
}

/**
 * @see \Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase
 */
function assertModelMissing($model) {
    return test()->assertModelMissing($model);
}