<?php

namespace App\Console\Commands;

use App\Participant;
use Arr;
use Crypt;
use DrawHandler;
use Hashids;
use Illuminate\Console\Command;

class FixOrganizer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:organizer {url : The URL received by one of the participants to write to their santa} {email : The correct email of the organizer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix an organizer email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Too much copy paste of code from routes and middleware. Need to find a way to regroup those.
        sscanf(
            str_replace('#', ' ', $this->argument('url')), // sscanf will not split with '#' but will with a space
            route('dearsanta', ['santa' => '%s']).' %s',
            $id,
            $hash
        );

        $key = base64_decode($hash);
        Crypt::setKey($key);

        $id = Arr::get(Hashids::connection(config('hashids.default'))->decode($id), 0, $id);
        $draw = Participant::find($id)->draw;

        $draw->organizer->email = $this->argument('email');
        $draw->organizer->save();

        DrawHandler::contactOrganizer($draw, false);
        $this->info('Organizer Recap sent');

        DrawHandler::contactParticipant($draw->organizer);
        $this->info('Organizer Participant mail sent');
    }
}
