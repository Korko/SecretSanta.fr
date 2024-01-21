<?php

namespace App\Actions;

use App\Models\Participant;
use App\Notifications\ConfirmWithdrawal;
use App\Notifications\DearSanta;
use App\Notifications\TargetWithdrawn;
use Exception;

class WithdrawParticipant
{
    public function withdraw(Participant $participant)
    {
        // A -> B -> C => A -> C
        $participant->santa->target()->associate($participant->target);
        $participant->santa->save();

        try {
            $participant->santa->notify(new TargetWithdrawn($participant, $participant->target));
            $participant->target->dearSantas->each(function ($dearSanta) use ($participant) {
                $participant->santa->notify(new DearSanta($dearSanta));
            });
        } catch(Exception $e) {
            //TODO
        }

        $participant->delete();
        try {
            $participant->notify(new ConfirmWithdrawal);
        } catch(Exception $e) {
            //TODO
        }
    }
}
