<?php

namespace App\Http\Controllers;

use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use App\Http\Requests\RandomFormRequest;
use DrawHandler;

class RandomFormController extends Controller
{
    public function view()
    {
        return response()->view('randomForm');
    }

    public function handle(RandomFormRequest $request)
    {
        $draw = DrawHandler::withParticipants($request->input('participants'))
            ->withTitle($request->input('title'))
            ->withBody($request->input('content-email'))
            ->withExpiration($request->input('data-expiration'))
            ->save();

        $draw->createMetric('new_draw')
            ->addExtra('participants', count($draw->participants));

        $draw->organizer->notify(new OrganizerRecap);
        foreach ($draw->participants as $participant) {
            $participant->notify(new TargetDrawn);
        }

        return response()->json([
            'message' => trans('message.sent')
        ]);
    }

    protected function isNextDrawSolvable(Draw $draw): bool
    {
        try {
            $exclusions = [];

            $draw->participants->each(function (Participant $participant) use (&$exclusions) {
                $exclusions[$participant->id] = array_merge(
                    $participant->exclusions->pluck('id')->all(),
                    [$participant->target->id]
                );
            });

            Solver::one($draw->participants->pluck(null, 'id')->all(), $exclusions);

            return true;
        } catch (SolverException $exception) {
            return false;
        }
    }

    public function faq()
    {
        return response()->view('faq', [
            'questions' => __('faq.questions'),
        ]);
    }
}
