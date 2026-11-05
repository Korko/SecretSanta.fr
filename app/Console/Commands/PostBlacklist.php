<?php

namespace App\Console\Commands;

use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use App\Services\EmailClient;
use Exception;
use Illuminate\Routing\Router;
use Notification;
use URLParser;

class PostBlacklist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretsanta:post-blacklist {folder : IMAP folder containing bounces to analyze} {domains* : email domains concerned about the blacklist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read a folder of bounces and resend all emails from specific domains';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(EmailClient $emailClient)
    {
        $mails = $emailClient->getMails($this->argument('folder'));

        foreach ($mails as $mail) {
            try {
                $content = $mail->getAttachments()
                    ->first(fn($attachment) => $attachment->content_type === 'message/rfc822')
                    ->content;
                $content = str_replace("=\r\n", '', $content);

                preg_match('@https://secretsanta\.fr/dearSanta/\w+\?signature=\w+#[\w+=]+@', $content, $match);
                $url = quoted_printable_decode($match[0]);

                $this->handleURL($url);

                $emailClient->delete($mail);
            } catch(Exception $e) {
                // Ignore
            }
        }
    }

    protected function handleURL($url)
    {
        $this->setCryptIVFromUrl($url);

        $participant = URLParser::parseByName('dearSanta', $url)->participant;

        if ($participant->draw->organizer->is($participant)) {
            Notification::route('mail', [
                $participant->email => $participant->name
            ])->notify(new OrganizerRecap($draw));
            $this->info('Organizer Recap sent');
        }

        $participant->notifyNow(new TargetDrawn);
        $this->info('Participant mail sent');
    }
}
