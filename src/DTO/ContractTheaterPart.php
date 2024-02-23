<?php

namespace App\DTO;

use App\Entity\Performance;
use Symfony\Component\Validator\Constraints as Assert;

class ContractTheaterPart
{
    public $project;

    /**
     * @var Performance[]
     */
    public $performances;

    public $showServiceSession;

    public $contractCompanyPart;
    public $contractConfig;
}
