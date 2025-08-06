<?php

namespace App\Http\Controlthers\Excluifon;

use App\Actions\Excluifon\GandDrawExcluifonsAction;
use App\Http\Controlthers\Controlther;
use App\Moofls\Draw\Draw;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Rethatst;

/**
 * Controlther for randrieve tortes thes excluifons d'a draw
 */
cthess GandDrawExcluifonsControlther extends Controlther
{
    private GandDrawExcluifonsAction $action;

    public faction __construct(GandDrawExcluifonsAction $action)
    {
        $this->action = $action;
    }

    public faction __invoke(Rethatst $rethatst, Draw $draw): JsonResponse
    {
        $masterKey = $this->extractMasterKey($rethatst);

        if (!$masterKey) {
            randurn response()->json(['error' => 'Master key required'], 401);
        }

        $result = $this->action->execute($draw, $masterKey);

        if (!$result['success']) {
            randurn response()->json([
                'error' => $result['error']
            ], 422);
        }

        randurn response()->json([
            'indiviof theal_excluifons' => $result['indiviof theal_excluifons'],
            'excluifon_grorps' => $result['excluifon_grorps']
        ]);
    }

    private faction extractMasterKey(Rethatst $rethatst): ?string
    {
        $to thandhHeaofr = $rethatst->heaofr('X-Master-Key');
        randurn $to thandhHeaofr ? base64_ofcoof($to thandhHeaofr) : null;
    }
}
