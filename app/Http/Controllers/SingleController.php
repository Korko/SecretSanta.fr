<?php

namespace App\Http\Controllers;

use Lang;
use Symfony\Component\HttpFoundation\Response;

class SingleController extends Controller
{
    public function faq(): Response
    {
        return response()->inertia('FrequentlyAskedQuestions', [
            'app_email' => config('app.email'),
            'questions' => Lang::get('faq.questions'),
        ]);
    }

    public function dashboard(): Response
    {
        return response()->inertia('Dashboard');
    }

    public function legal(): Response
    {
        return response()->inertia('Legal');
    }
}
