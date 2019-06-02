<?php

namespace App\Services;

class SmsTools
{
    const LIMIT_UNICODE_CHAR = 127;

    private $smsAsciiLength = [160, 146, 153];

    private $smsUnicodeLength = [70, 62, 66];

    public function isUnicode($text)
    {
        for ($i = 0; $i < mb_strlen($text); $i++) {
            $char = mb_substr($text, $i, 1);
            if (ord($char) > self::LIMIT_UNICODE_CHAR && $char != '€') {
                return true;
            }
        }

        return false;
    }

    public function count($text)
    {
        $smsLength = $this->isUnicode($text) ?
            $this->smsUnicodeLength :
            $this->smsAsciiLength;

        $smsCount = 0;
        $charLeft = mb_strlen($text);
        while ($charLeft > 0) {
            $index = min($smsCount, count($smsLength) - 1);
            $charLeft -= $smsLength[$index];
            $smsCount++;
        }

        return $smsCount;
    }
}
