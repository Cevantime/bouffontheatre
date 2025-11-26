<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Reservation extends Constraint
{
    public $message = 'Votre réservation comporte plus de places ({{ sum }}) que disponible ({{ available }})';

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
