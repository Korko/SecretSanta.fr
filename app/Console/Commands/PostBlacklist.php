<?php

namespace App\Console\Commands;

use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use App\Services\EmailClient;
use Exception;
use Illuminate\Support\Str;
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

                $pattern = '@'.preg_quote(config('app.url'), '@').'/dearSanta/\w+\?signature=\w+#[\w+=]+@';
                if (! preg_match($pattern, $content, $match)) {
                    continue;
                }
                $url = quoted_printable_decode($match[0]);

                if ($this->handleURL($url)) {
                    $emailClient->delete($mail);
                }
            } catch (Exception $e) {
                $this->warn('Skipped a bounce: '.$e->getMessage());
            }
        }
    }

    protected function handleURL($url): bool
    {
        $this->setCryptIVFromUrl($url);

        $participant = URLParser::parseByName('dearSanta', $url)->participant;

        $domains = array_map(fn ($domain) => '@'.ltrim(strtolower($domain), '@'), $this->argument('domains'));
        if (! Str::endsWith(strtolower($participant->email), $domains)) {
            return false;
        }

        if ($participant->draw->organizer->is($participant)) {
            Notification::route('mail', [
                $participant->email => $participant->name
            ])->notify(new OrganizerRecap($participant->draw));
            $this->info('Organizer Recap sent');
        }

        $participant->notifyNow(new TargetDrawn);
        $this->info('Participant mail sent');

        return true;
    }
}
