<?php

namespace App\Validator;

use App\Service\PhoneService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PhoneValidator extends ConstraintValidator
{
    public function __construct(
        private PhoneService $phoneService
    )
    {
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\Phone $constraint */

        if (null === $value || '' === $value) {
            return;
        }


        if (!$this->phoneService->checkIsPhone($value)) {
            // TODO: implement the validation here
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
