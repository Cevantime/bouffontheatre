<?php

namespace App\Service;

class PhoneService
{
    const PHONE_REGEX = "#^(?:(?:\+|00)33|0)\s*([1-9](?:[\s.-]*\d){8})$#";

    public function checkIsPhone($phone): bool
    {
        return preg_match(self::PHONE_REGEX, trim($phone)) === 1;
    }

    public function formatPhone($phone)
    {
        $match = preg_match(self::PHONE_REGEX, trim($phone), $matches);
        if($match) {
            return implode(' ', str_split(str_replace(' ', '', '0'.$matches[1]), 2));
        } else {
            return $phone;
        }
    }
}
