<?php

namespace App\DTO;

use App\Validator\Phone;
use App\Validator\Siret;
use Symfony\Component\Validator\Constraints as Assert;

class ContractCompanyPartAdmin
{
    #[Assert\Length(max: 150)]
    public ?string $companyName = null;

    #[Siret]
    public ?string $companySiret = null;

    #[Assert\Length(max: 20)]
    public ?string $companyApe = null;

    #[Assert\Length(max: 100)]
    public ?string $companyLicense = null;

    #[Assert\Length(max: 150)]
    public ?string $companyPresident = null;

    public ?string $companyAddress = null;

    #[Assert\Length(max: 100)]
    public ?string $companyAssurance = null;

    #[Phone]
    public ?string $companyPhone = null;

    #[Assert\Length(max: 150)]
    public ?string $showName = null;

    #[Assert\Length(max: 150)]
    public ?string $showAuthor = null;

    #[Assert\Length(max: 150)]
    public ?string $showDirector = null;

    #[Assert\Type("int")]
    public ?int $showArtistCount = null;

    #[Assert\Iban]
    public ?string $showRib = null;

    #[Assert\Type('digit')]
    #[Assert\NotBlank]
    public $showDuration;

    #[Assert\Type('digit')]
    #[Assert\NotBlank]
    public $showMaxDuration;

}
