<?php

namespace App\Http\Controllers;

use Lang;
use URL;

class SingleController extends Controller
{
    public function faq()
    {
        return response()->inertia('FrequentlyAskedQuestions.vue', [
            'routes' => [
                'form' => URL::route('form.index'),
                'dashboard' => URL::route('dashboard'),
            ],
            'app_email' => config('app.email'),
            'questions' => Lang::get('faq.questions'),
        ]);
    }

    public function dashboard()
    {
        return response()->inertia('Dashboard.vue', [
            'routes' => [
                'form' => URL::route('form.index'),
                'faq' => URL::route('faq'),
            ],
        ]);
    }

    public function legal()
    {
        return response()->inertia('Legal.vue', [
            'routes' => [
                'form' => URL::route('form.index'),
                'dashboard' => URL::route('dashboard'),
            ],
        ]);
    }
}
