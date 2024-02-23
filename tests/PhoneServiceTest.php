<?php

namespace App\Tests;

use App\Form\DataTransformer\PhoneTransformer;
use App\Service\PhoneService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PhoneServiceTest extends KernelTestCase
{
    public function testTransformIrregularSpaces(): void
    {
        self::bootKernel();
        $phoneService = self::getContainer()->get(PhoneService::class);
        $testedPhone = "01  23 456 78  9";
        $this->assertTrue($phoneService->checkIsPhone($testedPhone));
        $this->assertEquals("01 23 45 67 89", $phoneService->formatPhone($testedPhone));
    }

    public function testNotPhone(): void
    {
        self::bootKernel();
        $phoneService = self::getContainer()->get(PhoneService::class);
        $testedPhone = " ++01 a3 456 79";
        self::assertFalse($phoneService->checkIsPhone($testedPhone));
        self::assertNull($phoneService->formatPhone($testedPhone));
    }

    public function testInternationalNotation(): void
    {
        self::bootKernel();
        $phoneService = self::getContainer()->get(PhoneService::class);
        $testedPhone = "+331  23 456 78  9";
        $this->assertTrue($phoneService->checkIsPhone($testedPhone));
        $this->assertEquals("01 23 45 67 89", $phoneService->formatPhone($testedPhone));
    }
}
