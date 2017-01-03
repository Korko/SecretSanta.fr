<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

class RequestCase extends TestCase
{
    // Ignore CSRF Validation
    use WithoutMiddleware;

    public function ajaxPost($url, $postArgs, $code = 200)
    {
        $server = ['HTTP_X-Requested-With' => 'XMLHttpRequest'];
        $response = $this->call('POST', $url, $postArgs, [], [], $server);
        $this->assertEquals($code, $response->status());

        return json_decode($response->content(), true);
    }
}
