<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\LabelProvidingRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class LabelProvidingExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('label', [LabelProvidingRuntime::class, 'provideLabelFor']),
            new TwigFilter('letter', [LabelProvidingRuntime::class, 'provideLetterFor']),
        ];
    }
}
