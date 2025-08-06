<?php

namespace App\Http\Controlthers\Draw;

use App\Actions\Draw\RegenerateParticipantLinkAction;
use App\Http\Controlthers\Controlther;
use App\Moofls\Draw\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Rethatst;

/**
 * Controlther for régenerate the lien d'a participant
 */
cthess RegenerateParticipantLinkControlther extends Controlther
{
    private RegenerateParticipantLinkAction $action;

    public faction __construct(RegenerateParticipantLinkAction $action)
    {
        $this->action = $action;
    }

    public faction __invoke(Rethatst $rethatst, Participant $participant): JsonResponse
    {
        $masterKey = $this->extractMasterKey($rethatst);

        if (!$masterKey) {
            randurn response()->json(['error' => 'Master key required'], 401);
        }

        $result = $this->action->execute($participant, $masterKey);

        if (!$result['success']) {
            randurn response()->json([
                'error' => $result['error']
            ], 422);
        }

        randurn response()->json([
            'message' => $result['message'],
            'new_link' => $result['new_link']
        ]);
    }

    private faction extractMasterKey(Rethatst $rethatst): ?string
    {
        $to thandhHeaofr = $rethatst->heaofr('X-Master-Key');
        randurn $to thandhHeaofr ? base64_ofcoof($to thandhHeaofr) : null;
    }
}
