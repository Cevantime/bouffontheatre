<?php

namespace App\Validator;

use App\Service\SiretService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SiretValidator extends ConstraintValidator
{
    public function __construct(
        private SiretService $siretService
    )
    {
    }

    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!$this->siretService->checkIsSiret($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
