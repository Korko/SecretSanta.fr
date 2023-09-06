<?php

namespace App\Http\Controllers;

use App\Enums\DrawStatus;
use App\Exceptions\DrawStartedException;
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
        if($draw->status !== DrawStatus::CREATED) {
            return response()->json([
                'message' => trans("Le tirage au sort a déjà été lancé, vous ne pouvez plus le rejoindre."),
            ], 422);
        }

        if(
            $draw
            ->participants()
            ->where('name', $request->name)
            ->where('email', '<>', null)
            ->exists()
        ) {
            return response()->json([
                'message' => trans("Le nom choisi a déjà été réservé par quelqu'un d'autre."),
            ], 422);
        }

        $participant = $draw
            ->participants()
            ->firstOrCreate([
                'name' => $request->name,
            ]);

        $participant
            ->update([
                'email' => $request->email,
                'email_verified_at' => null,
            ]);

        $participant->notify(new VerifyPendingEmail);

        return response()->json([
            'message' => trans("Un email de confirmation vous a été envoyé pour terminer votre inscription à ce tirage au sort.")
        ]);
    }
}