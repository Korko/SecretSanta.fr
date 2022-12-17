<?php

namespace App\Http\Controllers;

use App\Facades\DrawCrypt;
use App\Http\Requests\FixOrganizerRequest;
use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use Arr;
use Carbon\Carbon;
use Notification;
use URL;
use URLParser;

class FixOrganizerController extends Controller
{
    public function view()
    {
        return response()->view('fixOrganizer', [
            'fixUrl' => URL::route('fixOrganizer.handle')
        ]);
    }

    public function handle(FixOrganizerRequest $request)
    {
        $hash = Arr::get(explode('#', $request->input('url'), 2), 1);
        $key = base64_decode($hash);
        DrawCrypt::setIV($key);

        try {
            $participant = URLParser::parseByName('dearSanta', $request->input('url'))->participant;
        } catch (\Exception $e) {
        }

        if (!isset($participant)) {
            return response()->json([
                'errors' => [
                    'url' => [
                        trans('error.fixOrganizer.drawNotFoundOrExpired'),
                    ],
                ],
            ], 422);
        }

        $draw = $participant->draw;

        if (levenshtein($request->input('email'), $draw->organizer_email) > 3) {
            return response()->json([
                'errors' => [
                    'email' => [
                        trans('error.fixOrganizer.distanceEmailTooBig'),
                    ],
                ],
            ], 422);
        }

        if ($draw->organizer->mail->updated_at->diffInSeconds(Carbon::now()) <= config('mail.resend_delay')) {
            return response()->json([
                'errors' => [
                    'email' => [
                        trans('error.fixOrganizer.resendDelayTooShort'),
                    ],
                ],
            ], 422);
        }

        $participantOrganizer = false;
        if ($draw->organizer->email === $draw->organizer_email) {
            $participantOrganizer = true;
        }

        $draw->organizer_email = $request->input('email');
        $draw->save();

        Notification::route('mail', [
            $draw->organizer_email => $draw->organizer_name
        ])->notify(new OrganizerRecap($draw));

        if ($participantOrganizer) {
            if ($this->argument('email')) {
                $draw->organizer->email = $this->argument('email');
                $draw->organizer->save();
            }

            $draw->organizer->notifyNow(new TargetDrawn);
        }

        return response()->json([
            'message' => trans('message.fixedOrganizer'),
        ]);
    }
}
