<?php

namespace App\Http\Controllers;

use App\Enums\DrawStatus;
use App\Http\Requests\JoinDrawRequest;
use App\Models\Draw;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use VerifyPendingEmail;

class JoinDrawController extends Controller
{
    // Hashed route
    public function join(Draw $draw): Response
    {
        if($draw->status !== DrawStatus::CREATED) {
            return view('pending.locked', [
                'draw' => $draw,
            ]);
        }

        return view('pending.join', [
            'draw' => $draw,
        ]);
    }

    public function handleJoin(Draw $draw, JoinDrawRequest $request): JsonResponse
    {
        // TODO: If a visitor is trying to join but in the mean time, the draw was started, maybe fail gracefully?
        throw_unless($draw->status === DrawStatus::CREATED, ModelNotFoundException::class);

        // TODO: Check chosen name is not already locked

        $pendingParticipant = $draw
            ->participants()
            ->firstOrCreate([
                'name' => $request->name,
            ]);

        $pendingParticipant
            ->update([
                'email' => $request->email,
                'email_verified_at' => null,
            ]);

        $pendingParticipant->notify(new VerifyPendingEmail);

        return response()->json([
            'message' => 'foobar'
        ]);
    }
}
