<?php

namespace Tests\Feature;

use Korko\SecretSanta\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

class RequestCase extends TestCase
{
    public function ajaxPost($url, $postArgs, $code = 200)
    {
        $server = ['HTTP_X-Requested-With' => 'XMLHttpRequest'];
        $response = $this->call('POST', $url, $postArgs, [], [], $server);
        $this->assertEquals($code, $response->status(), $response->getContent());

        return json_decode($response->content(), true);
    }
}
