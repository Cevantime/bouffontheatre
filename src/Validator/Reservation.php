<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Reservation extends Constraint
{
    public $message = 'Votre réservation comporte plus de places ({{ sum }}) que disponible ({{ available }})';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
