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

    #[Assert\Type('digit')]
    public $showDuration;

    #[Assert\Type('digit')]
    public $showMaxDuration;


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
    public $contractCity;

    #[Amount]
    public $tva;
}
