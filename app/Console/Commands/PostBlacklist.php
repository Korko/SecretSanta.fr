<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use App\Services\EmailClient;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use URLParser;

#[Description('Read a folder of bounces and resend all emails from specific domains')]
#[Signature('secretsanta:post-blacklist {folder : IMAP folder containing bounces to analyze} {domains* : email domains concerned about the blacklist}')]
final class PostBlacklist extends Command
{
    use ParsesDrawUrl;

    /**
     * Execute the console command.
     */
    public function handle(EmailClient $emailClient): void
    {
        $mails = $emailClient->getMails($this->argument('folder'));

        foreach ($mails as $mail) {
            try {
                $content = $mail->getAttachments()
                    ->first(fn ($attachment): bool => $attachment->content_type === 'message/rfc822')
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

    private function handleURL(string $url): bool
    {
        $this->setCryptIVFromUrl($url);

        $participant = URLParser::parseByName('dearSanta', $url)->participant;

        $domains = array_map(fn ($domain): string => '@'.ltrim(strtolower($domain), '@'), $this->argument('domains'));
        if (! Str::endsWith(strtolower($participant->email), $domains)) {
            return false;
        }

        if ($participant->draw->organizer->is($participant)) {
            Notification::route('mail', [
                $participant->email => $participant->name,
            ])->notify(new OrganizerRecap($participant->draw));
            $this->info('Organizer Recap sent');
        }

        $participant->notifyNow(new TargetDrawn);
        $this->info('Participant mail sent');

        return true;
    }
}
