<?php

namespace App\Console\Commands;

use DrawCrypt;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\table;

#[Description('List draw participants')]
#[Signature('secretsanta:list-draw {--targets} {url? : The URL received by one of the participants to write to their santa or the link to the organizer panel}')]
final class ListDraw extends Command
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

        info('Draw #'.$draw->id.' - '.$draw->mail_title.' ('.$draw->expires_at.')');
        info('Organizer: '.$draw->organizer_name.' <'.$draw->organizer_email.'>');
        note(URL::signedRoute('organizerPanel', ['draw' => $draw->hash]).'#'.base64_encode(DrawCrypt::getIV()));
        $this->newLine();

        table(
            array_merge(['ID', 'Name', 'Email', 'Status'], $this->option('targets') ? ['Target'] : []),
            $draw->participants()
                ->with(['mail', 'target'])
                ->get()
                ->map(fn ($col): array => Arr::only($col->toArray(), ['id', 'name', 'email']) + ['delivery_status' => Arr::get($col->toArray(), 'mail.delivery_status')] + ($this->option('targets') ? ['target' => $col->target->id.' ('.$col->target->name.')'] : []))
                ->all()
        );
    }
}
