<?php

namespace App\Http\Controllers;

use Lang;
use URL;

class SingleController extends Controller
{
    public function faq()
    {
        return static::renderWithInertia('FrequentlyAskedQuestions.vue', [
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

    }

    public function legal()
    {
        return static::renderWithInertia('Legal.vue', [
            'routes' => [
                'form' => URL::route('form.index'),
                'dashboard' => URL::route('dashboard'),
            ],
        ]);
    }
}
