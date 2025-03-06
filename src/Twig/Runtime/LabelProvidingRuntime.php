<?php

namespace App\Twig\Runtime;

use App\Service\LabelProvider;
use Twig\Extension\RuntimeExtensionInterface;

class LabelProvidingRuntime implements RuntimeExtensionInterface
{
    public function provideLabelFor($value)
    {
        return LabelProvider::provideLabelFor($value);
    }

    public function provideLetterFor(int $integer)
    {
        $alphabet = range('A', 'Z');

        return $alphabet[$integer - 1]; // returns D
    }
}
