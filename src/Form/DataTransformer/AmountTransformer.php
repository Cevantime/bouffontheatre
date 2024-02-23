<?php

namespace App\Form\DataTransformer;

use App\Service\AmountService;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class AmountTransformer implements DataTransformerInterface
{
    public function __construct(
        private AmountService $amountService
    )
    {
    }

    /**
     * @param mixed $value
     * @return mixed|null
     */
    public function transform(mixed $value)
    {
       return $this->amountService->formatStandardToFrenchAmount($value);
    }

    /**
     * @param mixed $value
     * @return mixed|null
     */
    public function reverseTransform(mixed $value)
    {
        return $this->amountService->formatFrenchToStandardAmount($value);
    }
}
