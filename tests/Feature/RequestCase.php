<?php

namespace Tests\Feature;

use Tests\TestCase;

class RequestCase extends TestCase
{
    public function ajaxPost($url, $postArgs, $code = 200)
    {
        $headers = [
            'Accept'           => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ];
        $response = $this->post($url, $postArgs, $headers);
        $this->assertEquals($code, $response->status(), $response->getContent());

        return json_decode($response->content(), true);
    }
}
