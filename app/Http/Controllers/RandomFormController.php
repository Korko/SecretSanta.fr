<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\SolverException;
use App\Http\Requests\RandomFormRequest;
use App\Models\Draw;
use App\Models\ParticipantLimit;
use App\Notifications\OrganizerRecap;
use App\Services\DrawFormHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use STS\Metrics\Facades\Metrics;

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

    public function handle(RandomFormRequest $request): JsonResponse
    {
        /** @var array{participant-organizer?: bool, organizer: array{name: string, email: string}, participants: list<array{name: string, email: string, exclusions?: list<int|string>}>, title: string, content-email: string, data-expiration: string} $safe */
        $safe = $request->validated();

        try {
            $drawForm = (new DrawFormHandler);

            if (! ($safe['participant-organizer'] ?? false)) {
                $drawForm->withOrganizer($safe['organizer']);
            }

            $draw = DB::transaction(function () use ($drawForm, $safe, $request): Draw {
                $draw = $drawForm
                    ->withParticipants($safe['participants'])
                    ->withTitle($safe['title'])
                    ->withBody($safe['content-email'])
                    ->withExpiration($safe['data-expiration'])
                    ->save();

                // Single use: the draw keeps the raised limit for the participants added later
                $limit = $request->participantLimit();
                if ($limit instanceof ParticipantLimit) {
                    throw_unless($limit->consume($draw), ValidationException::withMessages(['limit' => trans('error.participantLimit')]));
                    $draw->max_participants = $limit->max;
                    $draw->save();
                }

                return $draw;
            });

            // Taking part, the organizer gets the recap within their own target email (TargetDrawn)
            if (! ($safe['participant-organizer'] ?? false)) {
                $draw->notify(new OrganizerRecap($draw));
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
