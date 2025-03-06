<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class CurrencyExtensionRuntime implements RuntimeExtensionInterface
{
    private $currencyNumberFormatter;

    public function __construct()
    {
        $this->currencyNumberFormatter = new \NumberFormatter('fr_FR', \NumberFormatter::CURRENCY);
    }

    public function convertToEuroFormat($value)
    {
        return $this->currencyNumberFormatter->formatCurrency($value, 'EUR');
    }
}
