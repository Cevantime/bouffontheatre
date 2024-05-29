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
    private $performances;

    public $showServiceSession;

    public $contractCompanyPart;
    public $contractConfig;

    public function getPerformances()
    {
        return $this->performances;
    }

    public function setPerformances($performances)
    {
        $this->performances = $performances;
    }
}
