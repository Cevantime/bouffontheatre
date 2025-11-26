<?php

namespace App\Validator;

use App\Entity\Performance;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class QuotaValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var Quota $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        /** @var Performance $value */

        if($value->getReservationsSum() <= $value->getQuota()) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value->getQuota())
            ->setParameter('{{ booked }}', $value->getReservationsSum())
            ->addViolation()
        ;
    }
}
