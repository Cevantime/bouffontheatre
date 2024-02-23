<?php

namespace App\Service;

use function Symfony\Component\String\u;

class StringCallbacks
{
    public static function camelize(string $value)
    {
        return u($value)->camel()->__toString();
    }

    public static function snakify(string $value)
    {
        return u($value)->snake()->__toString();
    }
}
