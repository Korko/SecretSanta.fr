<?php

namespace Tests;

use App\Jobs\SendMail;
use Closure;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Mail\Mailable;
use Mail;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
}
