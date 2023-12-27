<?php

namespace App\Console\Commands;

use App\Models\Participant;
use App\Notifications\ConfirmWithdrawal;
use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use App\Traits\ParsesUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ChangeParticipant extends Command
{
    use ParsesUrl;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretsanta:change-participant {url : The URL received by one of the participants to write to their santa} {id : The participant id or ulid} {name : The new name of the participant} {email : The new email of the participant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change a participant name and email, send them their target and inform their santa';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $draw = $this->getDrawFromURL($this->argument('url'));

        $id = $this->argument('id');
        $participant = $draw->participants()->where(Str::isUlid($id) ? 'ulid' : 'id', $id)->firstOrFail();

        $participant->dearSantas()->lazy()->each->delete();

        $oldParticipant = (clone $participant);
        $participant->name = $this->argument('name');
        $participant->email = $this->argument('email');
        $participant->save();

        $oldParticipant->notify(new ConfirmWithdrawal);
        if ($participant->target instanceof Participant) {
            $participant->notify(new TargetDrawn);

            $participant->target->dearSantas->each(function ($dearSanta) use ($participant) {
                $participant->notify(new DearSanta($dearSanta));
            });
        }
        $this->info('Participants mails sent');

        if ($participant->santa instanceof Participant) {
            $participant->santa->notify(new TargetWithdrawn($oldParticipant, $participant));
        }
        $this->info('Santa informed');
    }
}
