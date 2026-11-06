<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Mail as MailModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Webklex\PHPIMAP\Message as EmailMessage;

final class MailTracker
{
    private const string BOUNCE = 'bounce';

    private const string CONFIRM = 'confirm';

    public function getBounceReturnPath($mail): string|array
    {
        return str_replace('*', self::BOUNCE.'-'.$mail->notification, config('mail.return_path'));
    }

    public function getConfirmReturnPath($mail): string|array
    {
        return str_replace('*', self::CONFIRM.'-'.$mail->notification, config('mail.return_path'));
    }

    public function handle(EmailMessage $unseenMail): void
    {
        $mail = $this->getMailFromEmail($unseenMail);

        if ($this->isEmailReceived($unseenMail)) {
            $mail->markAsReceived();
        } else {
            $mail->markAsError();
        }
    }

    public function isEmailReceived(EmailMessage $message): bool
    {
        $header = $message->getHeader();
        $autoSubmitted = (string) $header->get('auto_submitted');

        return
            $this->getNotificationType($message) === self::CONFIRM ||
            $header->has('x_autoreply') ||
            $header->has('x_autorespond') ||
            str_contains($autoSubmitted, 'auto-replied (vacation)') ||
            str_contains($autoSubmitted, 'auto-replied (zimbra');
    }

    private function getMailFromEmail(EmailMessage $unseenMail)
    {
        $notificationId = $this->getNotificationId($unseenMail);

        throw_if(empty($notificationId), ModelNotFoundException::class);

        return MailModel::query()->where('notification', $notificationId)->firstOrFail();
    }

    private function getNotificationType(EmailMessage $message): ?string
    {
        return Arr::get($this->parseRecipient($message), 0);
    }

    private function getNotificationId(EmailMessage $message): ?string
    {
        return Arr::get($this->parseRecipient($message), 1);
    }

    private function parseRecipient(EmailMessage $message): array
    {
        return $this->parseReturnPath(
            $this->getFirstRecipientAddress($message)
        );
    }

    private function getFirstRecipientAddress(EmailMessage $message): string
    {
        $recipient = $message->getTo()[0];

        $address = '';
        if (is_object($recipient)) {
            $address = $recipient->mailbox;

            if (str_contains($address, '@')) {
                $address = mb_stristr($recipient->mailbox, '@', true);
            }
        }

        return $address;
    }

    private function parseReturnPath(string $returnPath): array
    {
        return (array) sscanf(
            $returnPath,
            str_replace('*', '%[a-z]-%s', config('mail.return_path'))
        );
    }
}
