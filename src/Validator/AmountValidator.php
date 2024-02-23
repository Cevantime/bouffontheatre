<?php

namespace App\Validator;

use App\Service\AmountService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AmountValidator extends ConstraintValidator
{
    public function __construct(
        private AmountService $amountService
    )
    {
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\FrenchAmount $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if($this->amountService->checkIsFrenchAmount($value) || $this->amountService->checkIsEnglishAmount($value)) {
            return;
        }
        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
