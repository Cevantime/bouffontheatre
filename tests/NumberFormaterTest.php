<?php

namespace App\Tests;

use App\Service\AmountService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NumberFormaterTest extends KernelTestCase
{
    public function testPiToStandard(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $numberService = $container->get(AmountService::class);
        $number = '3,14';
        $float = $numberService->formatFrenchToStandardAmount($number);
        $this->assertEquals(3.14, $float);
    }

    public function testTotoToStandard(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $numberService = $container->get(AmountService::class);
        $number = 'toto';
        $float = $numberService->formatFrenchToStandardAmount($number);
        $this->assertEquals(0.0, $float);
    }
}
