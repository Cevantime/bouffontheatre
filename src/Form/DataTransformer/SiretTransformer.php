<?php

namespace App\Form\DataTransformer;

use App\Service\SiretService;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SiretTransformer implements DataTransformerInterface
{
    public function __construct(
        private SiretService $siretService
    )
    {
    }


    /**
     * @param mixed $value
     * @return mixed|null
     */
    public function transform(mixed $value)
    {
        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed|null
     */
    public function reverseTransform(mixed $value)
    {
        return $this->siretService->formatSiret($value);
    }
}
