<?php

namespace App\Console\Commands;

use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use URLParser;

class ChangeParticipant extends Command
{
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
        $this->setCryptIVFromUrl($this->argument('url'));

        $participant = URLParser::parseByName('dearSanta', $this->argument('url'))->participant;
        if ($participant) {
            $draw = $participant->draw;
        } else {
            $draw = URLParser::parseByName('organizerPanel', $this->argument('url'))->draw;
        }

        $participant = $draw->participants->find($this->argument('id'));

        $participant->dearSantas()->lazy()->each->delete();

        $participant->name = $this->argument('name');
        $participant->email = $this->argument('email');
        $participant->save();

        $participant->notifyNow(new TargetDrawn);
        $this->info('Participant mail sent');

        $participant->target->dearSantas->each(function ($dearSanta) use ($participant) {
            $participant->notify(new DearSanta($dearSanta));
        });

        $participant->santa->notify(new TargetWithdrawn);
        $this->info('Santa informed');
    }
}
