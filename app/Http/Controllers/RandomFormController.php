<?php

namespace App\Http\Controllers;

use App\Exceptions\SolverException;
use App\Http\Requests\RandomFormRequest;
use App\Notifications\OrganizerRecap;
use App\Services\DrawFormHandler;
use Arr;
use Notification;

class RandomFormController extends Controller
{
    public function view()
    {
        return response()->view('randomForm');
    }

    public function handle(RandomFormRequest $request)
    {
        $safe = $request->safe();

        try {
            $drawForm = (new DrawFormHandler);

            if (! Arr::get($safe, 'participant-organizer', false)) {
                $drawForm->withOrganizer($safe['organizer']);
            }

            $draw = $drawForm
                ->withParticipants($safe['participants'])
                ->withTitle($safe['title'])
                ->withBody($safe['content-email'])
                ->withExpiration($safe['data-expiration'])
                ->save();

            Notification::route('mail', [
                $draw->organizer_email => $draw->organizer_name,
            ])->notify(new OrganizerRecap($draw));

            $draw->createMetric('new_draw')
                ->addExtra('participants', count($draw->participants));

            return response()->json([
                'message' => trans('message.sent'),
            ]);
        } catch (SolverException $e) {
            return response()->json([
                'message' => trans('error.solution'),
            ], 422);
        }
    }

    public function faq()
    {
        return response()->view('faq', [
            'questions' => __('faq.questions'),
        ]);
    }
}
