<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use function Symfony\Component\String\u;

class SmartTruncateExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('strip_and_truncate', [$this, 'stripAndTruncate'], ['is_safe' => ['html']]),
        ];
    }

    public function stripAndTruncate($value, $letters, $ellipsis, $cut = false)
    {
        return u(html_entity_decode(strip_tags($value)))->truncate($letters, $ellipsis, $cut);
    }
}
