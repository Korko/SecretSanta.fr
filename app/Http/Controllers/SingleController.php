<?php

namespace App\Http\Controllers;

use Lang;

class SingleController extends Controller
{
    public function faq()
    {
        return response()->inertia('FrequentlyAskedQuestions', [
            'app_email' => config('app.email'),
            'questions' => Lang::get('faq.questions'),
        ]);
    }

    public function dashboard()
    {
        return response()->inertia('Dashboard');
    }

    public function legal()
    {
        return response()->inertia('Legal');
    }
}
