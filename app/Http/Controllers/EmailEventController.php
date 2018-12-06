<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use Metrics;

class EmailEventController extends Controller
{
    public function handle(Request $request)
    {
        Metrics::increment('email_bounced');

        $events = json_decode($request->getContent(), true);
        Log::debug($events);
        Log::debug($request->json()->all());
//        dump($request->getContent(), ((object) $request->json()->all())->name);
    }
}
