<?php

namespace App\Http\Controllers;

use App\Enums\DrawStatus;
use App\Http\Requests\AddNewParticipantRequest;
use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\DrawTitleChanged;
use App\Notifications\OrganizerNameChanged;
use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class DrawDashboardController extends Controller
{
    public function index(Draw $draw): Response
    {
        // TODO
        return response('test');
    }

    public function cancel(Draw $draw): Response
    {
        $draw->status = DrawStatus::CANCELED;
        $draw->save();

        // TODO
        return response('test');
    }

    public function changeTitle(Draw $draw, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:36773',
        ]);

        $draw->title = $validated['title'];
        $draw->save();

        $draw->santasNonOrganizer->each(function (Participant $participant) use ($draw) {
            $participant->notify(new DrawTitleChanged($draw));
        });

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function changeOrganizerName(Draw $draw, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:55',
                // New organizer name should not be the same as an actual participant
                // apart from themselves if they submit the same name
                function (string $attribute, mixed $value, Closure $fail) use ($draw) {
                    $otherNames = $draw
                        ->santasNonOrganizer
                        ->pluck('name');

                    if($otherNames->contains($value)) {
                        $fail('validation.custom.participant.name.not-unique');
                    }
                }
            ]
        ]);

        $draw->organizer->name = $validated['name'];
        $draw->organizer->save();

        $draw->santasNonOrganizer->each(function (Participant $participant) use ($draw) {
            $participant->notify(new OrganizerNameChanged($draw));
        });

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function changeOrganizerEmail(Draw $draw, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $draw->organizer->email = $validated['email'];
        $draw->organizer->email_verified_at = null;
        $draw->organizer->save();

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function confirmOrganizerEmail(Draw $draw): Response
    {
        $draw->organizer->email_verified_at = Carbon::now();
        $draw->organizer->save();

        // TODO
        return response('test');
    }

    public function participate(Draw $draw): JsonResponse
    {
        throw_if($draw->participant_organizer, new Exception('Organizer is already a participant'));

        $draw->participant_organizer = true;
        $draw->save();

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function withdraw(Draw $draw): JsonResponse
    {
        throw_unless($draw->participant_organizer, new Exception('Organizer is not a participant'));

        $draw->participant_organizer = false;
        $draw->save();

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function addParticipant(Draw $draw, AddNewParticipantRequest $request): JsonResponse
    {
        $draw->participants()->create([
            'name' => $request->safe()->name,
            'email' => $request->safe()->email ?? null,
        ]);

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function changeParticipantName(Draw $draw, Participant $participant, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:55',
                // TODO: Non participant organizer should not have the same name as an actual participant
                function (string $attribute, mixed $value, Closure $fail) use ($draw, $participant) {
                    $otherNames = $draw
                        ->santasNonOrganizer
                        ->pluck('name');

                    if($otherNames->contains($value)) {
                        $fail('validation.custom.participant.name.not-unique');
                    }
                }
            ]
        ]);

        $participant->name = $validated['name'];
        $participant->save();

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function removeParticipant(Draw $draw, Participant $participant): JsonResponse
    {
        throw_if($participant->is($draw->organizer), new Exception('Cannot remove organizer'));

        $participant->delete();

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function changeParticipantEmail(Draw $draw, Participant $participant, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $participant->email = $validated['email'];
        $participant->email_verified_at = null;
        $participant->save();

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function confirmParticipantEmail(Draw $draw, Participant $participant): Response
    {
        $participant->email_verified_at = Carbon::now();
        $participant->save();

        // TODO
        return response('test');
    }
}
