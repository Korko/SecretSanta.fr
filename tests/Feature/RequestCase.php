<?php

namespace Tests\Feature;

use Tests\TestCase;

class RequestCase extends TestCase
{
    public function ajaxPost($url, array $postArgs = [], $code = 200, $rawJSONContent = false)
    {
        $headers = [
            'Accept'           => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ];

        if ($rawJSONContent) {
            $headers['Content-Type'] = 'application/json';
            $postArgs = json_encode($postArgs);
        }

        $response = $this->call('POST', $url, [], [], [], $headers, $postArgs);
        $this->assertEquals($code, $response->status(), $response->getContent());

        return json_decode($response->content(), true);
    }
}
