<?php

namespace App\Form\DataTransformer;

use App\Service\PhoneService;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use function Symfony\Component\String\u;

class PhoneTransformer implements DataTransformerInterface
{
    public function __construct(
        private PhoneService $phoneService
    )
    {
    }

    /**
     * @param mixed $value
     * @return mixed|null
     */
    public function transform(mixed $value):mixed
    {
        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed|null
     */
    public function reverseTransform(mixed $value):mixed
    {
        return $this->phoneService->formatPhone($value);
    }
}
