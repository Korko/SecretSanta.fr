<?php

namespace App\Console\Commands;

use DrawCrypt;
use function Laravel\Prompts\{error, info, note, table};
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;

class ListDraw extends Command
{
    use ParsesDrawUrl;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretsanta:list-draw {--targets} {url? : The URL received by one of the participants to write to their santa or the link to the organizer panel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List draw participants';

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

        info('Draw #'.$draw->id.' - '.$draw->mail_title.' ('.$draw->expires_at.')');
        info('Organizer: '.$draw->organizer_name.' <'.$draw->organizer_email.'>');
        note(URL::signedRoute('organizerPanel', ['draw' => $draw->hash]).'#'.base64_encode(DrawCrypt::getIV()));
        $this->newLine();

        table(
            array_merge(['ID', 'Name', 'Email', 'Status'], $this->option('targets') ? ['Target'] : []),
            $draw->participants()
                ->with(['mail', 'target'])
                ->get()
                ->map(fn ($col) => Arr::only($col->toArray(), ['id', 'name', 'email']) + ['delivery_status' => Arr::get($col->toArray(), 'mail.delivery_status')] + ($this->option('targets') ? ['target' => $col->target->id.' ('.$col->target->name.')'] : []))
                ->toArray()
        );
    }
}
