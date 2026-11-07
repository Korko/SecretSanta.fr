<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\SolverException;
use App\Http\Requests\RandomFormRequest;
use App\Notifications\OrganizerRecap;
use App\Services\DrawFormHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use STS\Metrics\Facades\Metrics;

final class RandomFormController extends Controller
{
    public function view(): Response
    {
        return response()->view('randomForm');
    }

    public function handle(RandomFormRequest $request): JsonResponse
    {
        /** @var array{participant-organizer?: bool, organizer: array{name: string, email: string}, participants: list<array{name: string, email: string, exclusions?: list<int|string>}>, title: string, content-email: string, data-expiration: string} $safe */
        $safe = $request->validated();

        try {
            $drawForm = (new DrawFormHandler);

            if (! ($safe['participant-organizer'] ?? false)) {
                $drawForm->withOrganizer($safe['organizer']);
            }

            $draw = $drawForm
                ->withParticipants($safe['participants'])
                ->withTitle($safe['title'])
                ->withBody($safe['content-email'])
                ->withExpiration($safe['data-expiration'])
                ->save();

            // Taking part, the organizer gets the recap within their own target email (TargetDrawn)
            if (! ($safe['participant-organizer'] ?? false)) {
                Notification::route('mail', [
                    $draw->organizer_email => $draw->organizer_name,
                ])->notify(new OrganizerRecap($draw));
            }

            $draw->createMetric('new_draw')
                ->addExtra('participants', count($draw->participants));

            return response()->json([
                'message' => trans('message.sent'),
            ]);
        } catch (SolverException) {
            Metrics::create('solver_failed')
                ->addExtra('participants', count($safe['participants']));

            return response()->json([
                'message' => trans('error.solution'),
            ], 422);
        }
    }

    public function faq(): Response
    {
        return response()->view('faq', [
            'questions' => __('faq.questions'),
        ]);
    }
}
