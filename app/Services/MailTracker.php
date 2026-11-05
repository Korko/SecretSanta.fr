<?php

namespace App\Services;

use App\Models\Mail as MailModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Webklex\PHPIMAP\Message as EmailMessage;

class MailTracker
{
    protected const BOUNCE = 'bounce';

    protected const CONFIRM = 'confirm';

    public function getBounceReturnPath($mail)
    {
        return str_replace('*', self::BOUNCE.'-'.$mail->notification, config('mail.return_path'));
    }

    public function getConfirmReturnPath($mail)
    {
        return str_replace('*', self::CONFIRM.'-'.$mail->notification, config('mail.return_path'));
    }

    public function handle(EmailMessage $unseenMail)
    {
        $mail = $this->getMailFromEmail($unseenMail);

        if ($this->isEmailReceived($unseenMail)) {
            $mail->markAsReceived();
        } else {
            $mail->markAsError();
        }
    }

    protected function getMailFromEmail(EmailMessage $unseenMail)
    {
        $notificationId = $this->getNotificationId($unseenMail);

        if (empty($notificationId)) {
            throw new ModelNotFoundException;
        }

        return MailModel::where('notification', $notificationId)->firstOrFail();
    }

    public function isEmailReceived(EmailMessage $message)
    {
        $header = $message->getHeader();
        $autoSubmitted = (string) $header->get('auto_submitted');

        return (
            $this->getNotificationType($message) === self::CONFIRM ||
            $header->has('x_autoreply') ||
            $header->has('x_autorespond') ||
            str_contains($autoSubmitted, 'auto-replied (vacation)') ||
            str_contains($autoSubmitted, 'auto-replied (zimbra')
        );
    }

    protected function getNotificationType(EmailMessage $message): ?string
    {
        return Arr::get($this->parseRecipient($message), 0);
    }

    protected function getNotificationId(EmailMessage $message): ?string
    {
        return Arr::get($this->parseRecipient($message), 1);
    }

    protected function parseRecipient(EmailMessage $message): array
    {
        return $this->parseReturnPath(
            $this->getFirstRecipientAddress($message)
        );
    }

    protected function getFirstRecipientAddress(EmailMessage $message): string
    {
        $recipient = $message->getTo()[0];

        $address = '';
        if (is_object($recipient)) {
            $address = $recipient->mailbox;

            if (str_contains($address, '@')) {
                $address = stristr($recipient->mailbox, '@', true);
            }
        }

        return $address;
    }

    protected function parseReturnPath($returnPath)
    {
        return (array) sscanf(
            $returnPath,
            str_replace('*', '%[a-z]-%s', config('mail.return_path'))
        );
    }
}
