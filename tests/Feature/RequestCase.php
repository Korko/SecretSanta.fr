<?php

namespace Tests\Feature;

use NoCaptcha;
use Tests\TestCase;

class RequestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');

        NoCaptcha::shouldReceive('verifyResponse')->andReturn(true);
        NoCaptcha::makePartial(); // We don't want to mock the display
    }

    public function rawAjaxPost($url, array $postArgs = [], $headers = [])
    {
        $headers = $headers + [
            'Content-Type' => 'application/json',
        ];

        $postArgs = json_encode($postArgs);

        return $this->ajaxPost($url, $postArgs, $headers);
    }

    public function ajaxPost($url, array $postArgs = [], $headers = [])
    {
        $headers = $headers + [
            'Accept'           => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ];

        return $this->withHeaders($headers)->json('POST', $url, $postArgs);
    }

    /**
     * $participants = [
     * [
     * 'name'   => 'toto',
     * 'email'  => 'test@test.com',
     * 'target' => 1,
     * ],
     * [
     * 'name'   => 'tata',
     * 'email'  => 'test2@test.com',
     * 'target' => 2,
     * ],
     * [
     * 'name'   => 'tutu',
     * 'email'  => 'test3@test.com',
     * 'target' => 0,
     * ],
     * ];.
     */
    public function formatParticipants($participants)
    {
        $participants = array_map(function ($id) use ($participants) {
            return $participants[$id] + [
                'exclusions' => array_values(array_map('strval', array_diff(array_keys($participants), [$id], [$participants[$id]['target']]))),
            ];
        }, array_keys($participants));

        return $participants;
    }
}
