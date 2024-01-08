<?php

namespace App\DTO;

use DateTime;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class Period
{
    public DateTimeImmutable $from;
    public DateTimeImmutable $to;
}
