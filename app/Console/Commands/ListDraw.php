<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Participant;
use DrawCrypt;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Support\Facades\URL;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\table;

// Replaces show-draw (--targets --messages --body) and export-draw (--targets)
#[Description('Show a draw and its participants')]
#[Signature('secretsanta:list-draw
    {url? : The URL received by one of the participants to write to their santa or the link to the organizer panel}
    {--targets : Show the target of each participant}
    {--messages : How many messages each participant wrote to their santa and received from their target}
    {--body : The body of the target email}')]
final class ListDraw extends Command
{
    use ParsesDrawUrl;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $draw = $this->findOrAskDrawFromUrl($this->stringArgument('url'));
        } catch (Exception $exception) {
            error('Draw not found - '.$exception->getMessage());
            report($exception);

            return;
        }

        $targets = (bool) $this->option('targets');
        $messages = (bool) $this->option('messages');

        info('Draw #'.$draw->id.' - '.$draw->mail_title.' ('.$draw->expires_at.')');
        info('Organizer: '.$draw->organizer_name.' <'.$draw->organizer_email.'>');
        note(URL::signedRoute('organizerPanel', ['draw' => $draw->hash]).'#'.base64_encode(DrawCrypt::getIV()));
        $this->newLine();

        table(
            [
                'ID', 'Name', 'Email', 'Status',
                ...($targets ? ['Target'] : []),
                ...($messages ? ['Messages sent', 'Messages received'] : []),
            ],
            $draw->participants()
                ->with(['mail', 'target.dearSantas', 'dearSantas'])
                ->get()
                ->map(fn (Participant $participant): array => [
                    (string) $participant->id, $participant->name, $participant->email, $participant->mail->delivery_status->value,
                    ...($targets ? [$participant->target->id.' ('.$participant->target->name.')'] : []),
                    ...($messages ? [(string) $participant->dearSantas->count(), (string) $participant->target->dearSantas->count()] : []),
                ])
                ->all()
        );

        if ((bool) $this->option('body')) {
            $this->newLine();
            info('Body: '.$draw->mail_body);
        }
    }
}
