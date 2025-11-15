<?php

namespace App\DTO;

use App\Validator\Amount;
use App\Validator\Phone;
use App\Validator\Siret;
use Symfony\Component\Validator\Constraints as Assert;

class ContractGlobalConfig
{
    #[Assert\Length(max: 150)]
    public $theaterName;
    #[Assert\Length(max: 250)]
    public $theaterAddress;
    #[Siret]
    public $theaterSiret;
    #[Assert\Length(max: 150)]
    public $theaterPresident;
    #[Assert\Length(max: 150)]
    public $theaterLicense;
    #[Phone]
    public $theaterPhone;
    #[Assert\Email]
    public $theaterEmail;
    #[Phone]
    public $theaterBookingPhone;

    #[Amount]
    public $showFullPrice;

    #[Amount]
    public $showHalfPrice;


    #[Amount]
    public $showTaxFreePrice;

    #[Amount]
    public $showAppPrice;


    public $showTheaterAvailability;
    public $showInvitations;

    #[Amount]
    public $showTheaterShare;

    #[Amount]
    public $showCompanyShare;

    #[Amount]
    public $showCompanySharePercent;

    #[Amount]
    public $showTheaterSharePercent;

    #[Amount]
    public $showMinimumShare;

    #[Amount]
    public $rentPrice;

    public $contractCity;

    #[Amount]
    public $tva;

    #[Assert\Type(type: 'integer')]
    public $stageManagementInstallHourCount = 4;

    #[Assert\Type(type: 'integer')]
    public $stageManagementShowHourCount = 4;

    #[Amount]
    public $stageManagementInstallPrice = 20;
    #[Amount]
    public $stageManagementShowPrice = 50;
}
