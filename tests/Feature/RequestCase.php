<?php

namespace Tests\Feature;

use Tests\TestCase;

class RequestCase extends TestCase
{
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
}
