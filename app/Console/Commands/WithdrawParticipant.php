<?php

namespace App\Console\Commands;

use App\Notifications\DearSanta;
use App\Notifications\TargetWithdrawn;
use URLParser;

class WithdrawParticipant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretsanta:withdraw-participant {url : The URL received by one of the participants to write to their santa} {id : The participant id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Withdraw a participant and inform their santa';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setCryptIVFromUrl($this->argument('url'));

        $participant = URLParser::parseByName('dearSanta', $this->argument('url'))->participant;
        if($participant) {
            $draw = $participant->draw;
        } else {
            $draw = URLParser::parseByName('organizerPanel', $this->argument('url'))->draw;
        }

        $participant = $draw->participants->find($this->argument('id'));

        $santa = $participant->santa;
        $target = $participant->target;

        if ($santa->is($target)) {
            // Limit case
            throw new Exception('Cannot withdraw participant: their target is also their santa');
        }

        // A -> B -> C => A -> C
        $santa->target()->save($target);

        $santa->notify(new TargetWithdrawn);
        $target->dearSantas->each(function ($dearSanta) use ($santa) {
            $santa->notify(new DearSanta($dearSanta));
        });
        $this->info('Santa informed');

        $participant->delete();
        $this->info('Participant withdrawn');
    }
}
