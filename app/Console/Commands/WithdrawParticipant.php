<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Notifications\DearSanta;
use App\Notifications\TargetWithdrawn;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

use function Laravel\Prompts\error;

#[Description('Withdraw a participant and inform their santa')]
#[Signature('secretsanta:withdraw-participant {url : The URL received by one of the participants to write to their santa} {id : The participant id}')]
final class WithdrawParticipant extends Command
{
    use ParsesDrawUrl;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $draw = $this->findOrAskDrawFromUrl($this->argument('url'));
        } catch (Exception $exception) {
            error('Draw not found - '.$exception->getMessage());
            report($exception);

            return;
        }

        $participant = $draw->participants->find($this->argument('id'));

        $santa = $participant->santa;
        $target = $participant->target;

        // Limit case
        throw_if($santa->is($target), Exception::class, 'Cannot withdraw participant: their target is also their santa');

        // A -> B -> C => A -> C
        $santa->target()->save($target);

        $santa->notify(new TargetWithdrawn);
        $target->dearSantas->each(function ($dearSanta) use ($santa): void {
            $santa->notify(new DearSanta($dearSanta));
        });
        $this->info('Santa informed');

        $participant->delete();
        $this->info('Participant withdrawn');
    }
}
