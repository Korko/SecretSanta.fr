<?php

namespace App\Console\Commands;

use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use function Laravel\Prompts\error;

class FixParticipant extends Command
{
    use ParsesDrawUrl;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretsanta:fix-participant {url : The URL received by one of the participants to write to their santa} {id : The participant id} {email? : The correct email of the participant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix a participant email and send them again their target';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $draw = $this->findOrAskDrawFromUrl($this->argument('url'));
        } catch(\Exception $e) {
            error('Draw not found - ' . $e->getMessage());
            report($e);
            return;
        }

        $participant = $draw->participants->find($this->argument('id'));

        if ($this->argument('email')) {
            $participant->email = $this->argument('email');
            $participant->save();
        }

        $participant->notifyNow(new TargetDrawn);
        $this->info('Participant mail sent');

        $participant->target->dearSantas->each(function ($dearSanta) use ($participant) {
            $participant->notify(new DearSanta($dearSanta));
            $this->info('DearSanta mail sent');
        });
    }
}
