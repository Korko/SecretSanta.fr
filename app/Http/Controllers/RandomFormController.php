<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\OrganizerRecap;
use App\Http\Requests\RandomFormRequest;
use App\Services\DrawFormHandler;
use Exception;

class RandomFormController extends Controller
{
    public function view()
    {
        return response()->view('randomForm');
    }

    public function handle(RandomFormRequest $request)
    {
        try {
            $draw = (new DrawFormHandler())
                ->withParticipants($request->input('participants'))
                ->withTitle($request->input('title'))
                ->withBody($request->input('content-email'))
                ->withExpiration($request->input('data-expiration'))
                ->save();

            $draw->organizer->notify(new OrganizerRecap);

            $draw->createMetric('new_draw')
                ->addExtra('participants', count($draw->participants));

            return response()->json([
                'message' => trans('message.sent')
            ]);
        } catch(Exception $e) {
            return response()->json([
                'error' => trans('error.internal')
            ]);
        }
    }

    public function faq()
    {
        return response()->view('faq', [
            'questions' => __('faq.questions'),
        ]);
    }
}
