<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Models\DrawTracking;
use App\Models\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

// Without accounts, the signed URL is the credential: it only ever authorizes the one private channel of its
// draw or participant, whatever channel the client asks for (see routes/channels.php)
final class BroadcastAuthController extends Controller
{
    public function draw(Request $request, Draw $draw): JsonResponse
    {
        return $this->authorize($request, $draw->broadcastChannel());
    }

    public function participant(Request $request, Participant $participant): JsonResponse
    {
        return $this->authorize($request, $participant->broadcastChannel());
    }

    // No signed URL before the draw exists: the token kept by the browser that sent the form
    public function tracking(Request $request, DrawTracking $tracking): JsonResponse
    {
        abort_unless($tracking->checkToken($request->header('X-TRACKING-TOKEN')), 403);

        return $this->authorize($request, $tracking->broadcastChannel());
    }

    private function authorize(Request $request, string $channel): JsonResponse
    {
        $request->validate(['socket_id' => ['required', 'string', 'regex:/^\d+\.\d+$/']]);
        $request->merge(['channel_name' => 'private-'.$channel]);

        /** @var array<string, mixed> $auth */
        $auth = Broadcast::driver()->validAuthenticationResponse($request, true);

        return response()->json($auth);
    }
}
