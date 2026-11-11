<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Mail as MailModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Webklex\PHPIMAP\Address;
use Webklex\PHPIMAP\Message as EmailMessage;

final class MailTracker
{
    private const string BOUNCE = 'bounce';

    private const string CONFIRM = 'confirm';

    public function getBounceReturnPath(MailModel $mail): string
    {
        return str_replace('*', self::BOUNCE.'-'.$mail->notification, config()->string('mail.return_path'));
    }

    public function getConfirmReturnPath(MailModel $mail): string
    {
        return str_replace('*', self::CONFIRM.'-'.$mail->notification, config()->string('mail.return_path'));
    }

    public function handle(EmailMessage $unseenMail): void
    {
        $mail = $this->getMailFromEmail($unseenMail);

        if ($this->isEmailReceived($unseenMail)) {
            $mail->markAsReceived();
        } elseif ($this->isDeliveryDelayed($unseenMail)) {
            $mail->markAsDelayed();
        } else {
            $mail->markAsError();
        }
    }

    public function isEmailReceived(EmailMessage $message): bool
    {
        $header = $message->getHeader();
        $autoSubmitted = (string) $header?->get('auto_submitted');

        return
            $this->getNotificationType($message) === self::CONFIRM ||
            $header?->has('x_autoreply') ||
            $header?->has('x_autorespond') ||
            str_contains($autoSubmitted, 'auto-replied (vacation)') ||
            str_contains($autoSubmitted, 'auto-replied (zimbra');
    }

    // Delivery status notification (RFC 3464) saying the server keeps trying: "delayed", unless a recipient also "failed"
    public function isDeliveryDelayed(EmailMessage $message): bool
    {
        $body = $message->getRawBody();

        return preg_match('/^Action:\s*delayed\b/mi', $body) === 1
            && preg_match('/^Action:\s*failed\b/mi', $body) !== 1;
    }

    private function getMailFromEmail(EmailMessage $unseenMail): MailModel
    {
        $notificationId = $this->getNotificationId($unseenMail);

        throw_if(empty($notificationId), ModelNotFoundException::class);

        return MailModel::query()->where('notification', $notificationId)->firstOrFail();
    }

    private function getNotificationType(EmailMessage $message): ?string
    {
        $type = $this->parseRecipient($message)[0] ?? null;

        return is_string($type) ? $type : null;
    }

    private function getNotificationId(EmailMessage $message): ?string
    {
        $id = $this->parseRecipient($message)[1] ?? null;

        return is_string($id) ? $id : null;
    }

    /** @return array<int, mixed> */
    private function parseRecipient(EmailMessage $message): array
    {
        return $this->parseReturnPath(
            $this->getFirstRecipientAddress($message)
        );
    }

    private function getFirstRecipientAddress(EmailMessage $message): string
    {
        $recipient = $message->getTo()->first();

        return $recipient instanceof Address ? Str::before($recipient->mailbox, '@') : '';
    }

    /** @return array<int, mixed> */
    private function parseReturnPath(string $returnPath): array
    {
        return array_values((array) sscanf(
            $returnPath,
            str_replace('*', '%[a-z]-%s', config()->string('mail.return_path'))
        ));
    }
}
