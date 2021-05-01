<?php

namespace App\Services;

use App\Models\Mail as MailModel;

class MailTracker
{
    const BOUNCE = 'bounce';
    const CONFIRM = 'confirm';

    public function getBounceReturnPath($mail)
    {
        return str_replace('*', self::BOUNCE.'-'.$mail->id, config('mail.return_path'));
    }

    public function getConfirmReturnPath($mail)
    {
        return str_replace('*', self::CONFIRM.'-'.$mail->id, config('mail.return_path'));
    }

    public function handle($returnPath)
    {
        $params = $this->parseReturnPath($returnPath);

        if (!empty($params[0])) {
            $mail = MailModel::find($params[1]);

            if ($mail) {
                switch($params[0]) {
                    case self::BOUNCE:
                        // TODO: Determine depending on the bounce error, if the failure is temporary (4xx) or final (5xx)
                        // Auto-retry or not
                        $mail->markAsError();
                        break;
                    case self::CONFIRM:
                        $mail->markAsReceived();
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