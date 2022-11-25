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
            $mail->markAsBounced();
        }
    }

    protected function getMailFromEmail(EmailMessage $unseenMail)
    {
        $notificationId = $this->getNotificationId($unseenMail);

        if (empty($notificationId)) {
            throw new ModelNotFoundException();
        }

        return MailModel::where('notification', $notificationId)->first();
    }

    protected function isEmailReceived(EmailMessage $message)
    {
        return (
            $this->getNotificationType($message) === self::CONFIRM ||
            $message->getHeader()->get('X-Autoreply') ||
            $message->getHeader()->get('X-Autorespond') ||
            $message->getHeader()->get('Auto-Submitted') === 'auto-replied'
        );
    }

    protected function getNotificationType(EmailMessage $message): string|null
    {
        return Arr::get($this->parseRecipient($message), 0);
    }

    protected function getNotificationId(EmailMessage $message): string|null
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

        return is_object($recipient) ? stristr($recipient->mailbox, '@', true) : '';
    }

    protected function parseReturnPath($returnPath)
    {
        return (array) sscanf(
            $returnPath,
            str_replace('*', '%[a-z]-%s', config('mail.return_path'))
        );
    }

    protected function markMail(MailModel $mail, $type, EmailMessage $unseenMail)
    {
        if (
            $type === self::CONFIRM ||
            $unseenMail->getHeader()->get('X-Autoreply') ||
            $unseenMail->getHeader()->get('X-Autorespond') ||
            $unseenMail->getHeader()->get('Auto-Submitted') === 'auto-replied'
        ) {
            $mail->markAsReceived();
        } else {
            $mail->markAsError();
        }
    }
}
