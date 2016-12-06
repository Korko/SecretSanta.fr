<?php

namespace Korko\SecretSanta\Libs;

class SmsTools
{
    private $smsAsciiLength = [160, 146, 153];
    private $smsUnicodeLength = [70, 62, 66];

    protected function isUnicode($text)
    {
        for ($i = 0; $i < mb_strlen($text); $i++) {
            $char = mb_substr($text, $i, 1);
            if ($char > 127 && $char != '€') {
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
            $charLeft -= $smsCount < 3 ? $smsLength[$smsCount] : $smsLength[2];
            $smsCount++;
        }

        return $smsCount;
    }
}
