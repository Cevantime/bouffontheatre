<?php

namespace App\DTO;

use App\Entity\Paper;
use App\Validator\Phone;
use App\Validator\Siret;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class ContractCompanyPart
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

    #[Assert\Iban]
    public ?string $showRib = null;

    #[Assert\Length(max: 150)]
    public ?string $showName = null;

    public $showAuthors = null;

    public $showDirectors = null;

    public $showArtists = null;

    public $showBanner = null;
    public $showPoster = null;
    public $showMedia = [];

    public ?string $showExcerpt = null;

    public ?int $minAge = null;

    /** @var Paper[] $papers */
    public $papers;

    #[Assert\Type('digit')]
    #[Assert\NotBlank]
    public $showDuration;

    #[Assert\Type('digit')]
    #[Assert\NotBlank]
    public $showMaxDuration;

    public ?string $teaser;


    #[Assert\Length(max: 250)]
    public ?string $showPunchline;

    public ?string $showDescription;

    public bool $showHasBanner = false;
    public bool $showHasPoster = false;

    public ?string $showBannerUrl = null;
    public ?string $showPosterUrl = null;
}
