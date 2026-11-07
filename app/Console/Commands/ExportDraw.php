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

#[Description('List draw participants')]
#[Signature('secretsanta:export-draw {url? : The URL received by one of the participants to write to their santa or the link to the organizer panel}')]
final class ExportDraw extends Command
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

        info('Draw #'.$draw->id.' - '.$draw->mail_title.' ('.$draw->expires_at.')');
        info('Organizer: '.$draw->organizer_name.' <'.$draw->organizer_email.'>');
        note(URL::signedRoute('organizerPanel', ['draw' => $draw->hash]).'#'.base64_encode(DrawCrypt::getIV()));
        $this->newLine();

        table(
            ['Name', 'Target'],
            $draw->participants()
                ->with(['mail', 'target'])
                ->get()
                ->map(fn (Participant $col): array => [$col->name, $col->target->name])
                ->all()
        );
    }
}
