<?php

namespace App\DTO;

use App\Entity\Performance;
use App\Validator\Amount;
use Symfony\Component\Validator\Constraints as Assert;

class ContractAdminPart
{
    public $project;

    /**
     * @var Performance[]
     */
    private $performances;

    public $showServiceSession;

    public $contractType;

    public $status;
    public $fetchDataStatus;

    public $contractCompanyPart;
    public $contractTheaterConfig;

//    public bool $minimumShare;


    public function getPerformances()
    {
        return $this->performances;
    }

    public function setPerformances($performances)
    {
        $this->performances = $performances;
    }
}
