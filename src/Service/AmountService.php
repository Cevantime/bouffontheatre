<?php

namespace App\Service;

class AmountService
{
    const ENGLISH_AMOUNT_REGEX = "/^\d+(\.\d{1,2})?$/";
    const FRENCH_AMOUNT_REGEX = "/^\d+(\,\d{1,2})?$/";
    function checkIsFrenchAmount($value)
    {
        return preg_match(self::FRENCH_AMOUNT_REGEX, $value);
    }

    function checkIsEnglishAmount($value)
    {
        return preg_match(self::ENGLISH_AMOUNT_REGEX, $value);
    }
    public function formatFrenchToStandardAmount(string $frenchNumber): float
    {
        return floatval(number_format(floatval(str_replace(',','.', $frenchNumber)), 2, '.', ''));
    }

    public function formatStandardToFrenchAmount(float $float)
    {
        $decimals = intval($float) == $float ? 0 : 2;
        return number_format($float, $decimals, ',', '');
    }
}
