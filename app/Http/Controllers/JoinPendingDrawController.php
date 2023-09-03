<?php

namespace App\Http\Controllers;

use App\Enums\EmailAddressStatus;
use App\Enums\PendingDrawStatus;
use App\Http\Requests\JoinDrawRequest;
use App\Models\PendingDraw;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use VerifyPendingEmail;

class JoinPendingDrawController extends Controller
{
    // Hashed route
    public function join(PendingDraw $pending): Response
    {
        if($pending->status !== PendingDrawStatus::CREATED) {
            return view('pending.locked', [
                'pending' => $pending,
            ]);
        }

        return view('pending.join', [
            'pending' => $pending,
        ]);
    }

    public function handleJoin(PendingDraw $pending, JoinDrawRequest $request): JsonResponse
    {
        // TODO: If a visitor is trying to join but in the mean time, the draw was started, maybe fail gracefully?
        throw_unless($pending->status === PendingDrawStatus::CREATED, ModelNotFoundException::class);

        // TODO: Check chosen name is not already locked

        $pendingParticipant = $pending
            ->participants()
            ->firstOrCreate([
                'name' => $request->name,
            ]);

        $pendingParticipant
            ->update([
                'email' => $request->email,
                'email_status' => EmailAddressStatus::CREATED,
            ]);

        $pendingParticipant->notify(new VerifyPendingEmail);

        return response()->json([
            'message' => 'foobar'
        ]);
    }
}
