<?php

namespace App\Services;

use App\Models\Mail as MailModel;

class MailTracker
{
    protected const BOUNCE = 'bounce';

    protected const CONFIRM = 'confirm';

    public function getBounceReturnPath($mail)
    {
        return str_replace('*', self::BOUNCE.'-'.$mail->ulid, config('mail.return_path'));
    }

    public function getConfirmReturnPath($mail)
    {
        return str_replace('*', self::CONFIRM.'-'.$mail->ulid, config('mail.return_path'));
    }

    public function handle($returnPath)
    {
        $params = $this->parseReturnPath($returnPath);

        if (! empty($params[0])) {
            $mail = MailModel::where('ulid', stristr($params[1], '@', true) ?: $params[1])->first();

            if ($mail) {
                switch ($params[0]) {
                    case self::BOUNCE:
                        // TODO: Determine depending on the bounce error, if the failure is temporary (4xx) or final (5xx)
                        // Auto-retry or not
                        $mail->delivery_status = Mail::STATE_ERROR;
                        $mail->save();
                        break;
                    case self::CONFIRM:
                        $mail->delivery_status = Mail::STATE_RECEIVED;
                        $mail->save();
                        break;
                }
            }
        }
    }

    protected function parseReturnPath($returnPath)
    {
        return (array) sscanf(
            $returnPath,
            str_replace('*', '%[a-z]-%s', config('mail.return_path'))
        );
    }
}
