<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RandomFormRequest;
use App\Jobs\SolveDraw;
use App\Models\DrawTracking;
use App\Models\ParticipantLimit;
use DrawCrypt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class RandomFormController extends Controller
{
    public function view(Request $request): Response
    {
        // An administrator link (secretsanta:allow-participants) raises the limit for one draw
        $code = $request->string('limit')->value();
        $limit = ParticipantLimit::findUsable($code);

        return response()->view('randomForm', [
            'maxParticipants' => $limit->max ?? config()->integer('app.max_participants'),
            'limitCode' => $limit instanceof ParticipantLimit ? $code : null,
            'limitInvalid' => $code !== '' && ! $limit instanceof ParticipantLimit,
        ]);
    }

    // Answers at once: the draw is solved by a job (SolveDraw), the browser follows it with the tracking (#117)
    public function handle(RandomFormRequest $request): JsonResponse
    {
        /** @var array{participant-organizer?: bool, organizer: array{name: string, email: string}, participants: list<array{name: string, email: string, exclusions?: list<int|string>}>, title: string, content-email: string, data-expiration: string, data-location?: string|null, data-budget?: string|null} $safe */
        $safe = $request->validated();

        [$tracking, $token] = DrawTracking::start();

        dispatch(new SolveDraw($tracking->id, $safe, $request->participantLimit()?->id));

        // Refreshed: with a sync queue (tests, no worker), the draw is already solved
        $tracking->refresh();

        return response()->json([
            'message' => trans('message.sent'),
            'tracking' => [
                'id' => $tracking->uuid,
                // Only kept in this browser, never in a URL: gives the details and the private channel
                'token' => $token,
                // The draw secret, for the calls needing it later (as X-DRAW-SECRET)
                'secret' => base64_encode(DrawCrypt::getSecret()),
                'channel' => $tracking->broadcastChannel(),
                'authUrl' => route('tracking.broadcastAuth', ['tracking' => $tracking->uuid]),
                ...$tracking->details(),
            ],
        ], 202);
    }

    // Without the token, only whether the draw went through: the UUID may be shared
    public function tracking(Request $request, DrawTracking $tracking): JsonResponse
    {
        return response()->json($tracking->checkToken($request->header('X-TRACKING-TOKEN'))
            ? $tracking->details()
            : ['state' => $tracking->state->value]);
    }

    public function faq(): Response
    {
        return response()->view('faq', [
            'questions' => __('faq.questions'),
        ]);
    }
}
