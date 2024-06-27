<?php

namespace App\Validator;

use App\Entity\Reservation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ReservationValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\Reservation $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        /** @var Reservation $value */

        $performance = $value->getPerformance();

        $availableSum = $performance->getAvailableReservationsSum($value->getId() ? $value : null);
        $sumTarifs = $value->getSumTarifs();

        if ($availableSum >= $sumTarifs) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ sum }}', $sumTarifs)
            ->setParameter('{{ available }}', $availableSum)
            ->addViolation();
    }
}
