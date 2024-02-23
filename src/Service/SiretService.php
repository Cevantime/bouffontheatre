<?php

namespace App\Service;

class SiretService
{
    public const SIRET_LENGTH = 14;

    public function formatSiret($value)
    {
        if( ! $this->checkIsSiret($value)) {
            return $value;
        }

        return implode(' ', str_split(str_replace(' ', '',$value), 3));
    }

    public function checkIsSiret($value)
    {
        $siret = (string)$value;
        $siret = str_replace([' ', ' '], ['', ''], $siret);
        if (!is_numeric($siret) || \strlen($siret) !== self::SIRET_LENGTH) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < self::SIRET_LENGTH; ++$i) {
            if ($i % 2 === 0) {
                $tmp = ((int)$siret[$i]) * 2;
                $tmp = $tmp > 9 ? $tmp - 9 : $tmp;
            } else {
                $tmp = $siret[$i];
            }
            $sum += $tmp;
        }

        return !($sum % 10 !== 0);
    }
}
