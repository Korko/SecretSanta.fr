<?php

namespace App\Console\Commands;

use App\Notifications\ConfirmWithdrawal;
use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use App\Traits\ParsesUrl;
use Illuminate\Console\Command;

class ChangeParticipant extends Command
{
    use ParsesUrl;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretsanta:change-participant {url : The URL received by one of the participants to write to their santa} {id : The participant id} {name : The new name of the participant} {email : The new email of the participant}';

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

        $participant = $draw->participants->find($this->argument('id'));

        $participant->dearSantas()->lazy()->each->delete();

        $oldParticipant = (clone $participant);
        $participant->name = $this->argument('name');
        $participant->email = $this->argument('email');
        $participant->save();

        $oldParticipant->notify(new ConfirmWithdrawal);
        $participant->notify(new TargetDrawn);
        $this->info('Participants mails sent');

        $participant->target->dearSantas->each(function ($dearSanta) use ($participant) {
            $participant->notify(new DearSanta($dearSanta));
        });

        $participant->santa->notify(new TargetWithdrawn($oldParticipant, $participant));
        $this->info('Santa informed');
    }
}
