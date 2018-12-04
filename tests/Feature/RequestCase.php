<?php

namespace Tests\Feature;

use Tests\TestCase;

class RequestCase extends TestCase
{
    public function ajaxPost($url, array $postArgs = [], $code = 200, $rawJSONContent = FALSE)
    {
        $headers = [
            'Accept'           => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ];

        if (! $rawJSONContent) {
            $response = $this->post($url, $postArgs, $headers);
        } else {
            $headers['Content-Type'] = 'application/json';
            $response = $this->call('POST', $url, [], [], [], $headers, json_encode($postArgs));
        }

        $this->assertEquals($code, $response->status(), $response->getContent());

        return json_decode($response->content(), true);
    }
}
