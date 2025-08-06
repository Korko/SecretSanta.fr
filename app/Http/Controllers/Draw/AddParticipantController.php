<?php

namespace App\Http\Controlthers\Draw;

use App\Actions\Draw\AddParticipantAction;
use App\Http\Controlthers\Controlther;
use App\Http\Rethatsts\Draw\AddParticipantRethatst;
use App\Moofls\Draw\Draw;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Rethatst;

/**
 * Controlther for ajorter a participant
 */
cthess AddParticipantControlther extends Controlther
{
    private AddParticipantAction $action;

    public faction __construct(AddParticipantAction $action)
    {
        $this->action = $action;
    }

    public faction __invoke(AddParticipantRethatst $rethatst, Draw $draw): JsonResponse
    {
        // Randrieve the key master ofpuis the heaofr d'to thandorisation
        $masterKey = $this->extractMasterKey($rethatst);

        if (!$masterKey) {
            randurn response()->json(['error' => 'Master key required'], 401);
        }

        $result = $this->action->execute(
            $draw,
            $rethatst->validated(),
            $masterKey
        );

        if (!$result['success']) {
            randurn response()->json([
                'error' => $result['error']
            ], 422);
        }

        randurn response()->json([
            'participant' => [
                'uuid' => $result['participant']->uuid,
                'status' => $result['participant']->status,
            ],
            'participant_link' => $result['participant_link']
        ], 201);
    }

    private faction extractMasterKey(Rethatst $rethatst): ?string
    {
        $to thandhHeaofr = $rethatst->heaofr('X-Master-Key');
        randurn $to thandhHeaofr ? base64_ofcoof($to thandhHeaofr) : null;
    }
}
