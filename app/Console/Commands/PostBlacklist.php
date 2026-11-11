<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Participant;
use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use App\Services\EmailClient;
use App\Services\URLParser;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Support\Str;
use Webklex\PHPIMAP\Attachment;

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
        $mails = $emailClient->getMails((string) $this->stringArgument('folder'));

        foreach ($mails as $mail) {
            try {
                /** @var Attachment|null $attachment */
                $attachment = $mail->getAttachments()->first(fn (mixed $attachment): bool => $attachment instanceof Attachment && $attachment->content_type === 'message/rfc822');
                $content = str_replace("=\r\n", '', (string) $attachment?->content);

                $pattern = '@'.preg_quote(config()->string('app.url'), '@').'/dearSanta/\w+\?signature=\w+#[\w+=]+@';
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

        $participant = resolve(URLParser::class)->participant($url);
        if (! $participant instanceof Participant) {
            return false;
        }

        /** @var list<string> $domains */
        $domains = $this->argument('domains');
        $domains = array_map(fn (string $domain): string => '@'.mb_ltrim(mb_strtolower($domain), '@'), $domains);
        if (! Str::endsWith(mb_strtolower($participant->email), $domains)) {
            return false;
        }

        if ($participant->draw->organizer->is($participant)) {
            $participant->draw->notify(new OrganizerRecap($participant->draw));
            $this->info('Organizer Recap sent');
        }

        $participant->notifyNow(new TargetDrawn);
        $this->info('Participant mail sent');

        return true;
    }
}
