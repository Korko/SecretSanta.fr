<?php

if (! function_exists('base64url_encode')) {
    function base64url_encode($s) {
        return str_replace(['+', '/'], ['-', '_'], base64_encode($s));
    }
}

if (! function_exists('base64url_decode')) {
    function base64url_decode($s) {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $s));
    }
}