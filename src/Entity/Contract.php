<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
class Contract
{
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_SENT_TO_COMPANY = 'SENT_TO_COMPANY';
    const STATUS_FILLED_BY_COMPANY = 'FILLED_BY_COMPANY';
    const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_SENT_TO_COMPANY,
        self::STATUS_FILLED_BY_COMPANY
    ];
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: true)]
    private ?int $id = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $theaterName = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $theaterAddress = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $theaterSiret = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $theaterPresident = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $theaterEmail = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $theaterPhone = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $companySiret = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $companyApe = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $companyLicense = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $companyPresident = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $companyAddress = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $companyAssurance = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $companyPhone = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $showName = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $showAuthor = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $showDirector = null;

    #[ORM\Column(nullable: true)]
    private ?int $showArtistCount = null;

    #[ORM\Column(nullable: true)]
    private ?int $showDuration = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $showTheaterAvailability = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $theaterBookingPhone = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $showFullPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $showHalfPrice = null;

    #[ORM\Column(nullable: true)]
    private ?int $showMaxDuration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $showInvitations = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $showTheaterShare = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $showCompanyShare = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $showCompanySharePercent = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $showTheaterSharePercent = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $showMinimumShare = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $showServiceSession = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $showRib = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $contractCity = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $contractDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $contractSignatureDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $tva = null;

    #[ORM\Column(length: 20)]
    private ?string $status = self::STATUS_DRAFT;

    #[ORM\OneToMany(mappedBy: 'contract', targetEntity: Performance::class, orphanRemoval: true)]
    #[ORM\OrderBy(['performedAt' => 'ASC'])]
    private Collection $performances;

    #[ORM\ManyToOne(inversedBy: 'contracts')]
    private ?Project $relatedProject = null;

    public function __construct()
    {
        $this->performances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTheaterName(): ?string
    {
        return $this->theaterName;
    }

    public function setTheaterName(?string $theaterName): static
    {
        $this->theaterName = $theaterName;

        return $this;
    }

    public function getTheaterAddress(): ?string
    {
        return $this->theaterAddress;
    }

    public function setTheaterAddress(?string $theaterAddress): static
    {
        $this->theaterAddress = $theaterAddress;

        return $this;
    }

    public function getTheaterSiret(): ?string
    {
        return $this->theaterSiret;
    }

    public function setTheaterSiret(?string $theaterSiret): static
    {
        $this->theaterSiret = $theaterSiret;

        return $this;
    }

    public function getTheaterPresident(): ?string
    {
        return $this->theaterPresident;
    }

    public function setTheaterPresident(?string $theaterPresident): static
    {
        $this->theaterPresident = $theaterPresident;

        return $this;
    }

    public function getTheaterEmail(): ?string
    {
        return $this->theaterEmail;
    }

    public function setTheaterEmail(?string $theaterEmail): static
    {
        $this->theaterEmail = $theaterEmail;

        return $this;
    }

    public function getTheaterPhone(): ?string
    {
        return $this->theaterPhone;
    }

    public function setTheaterPhone(?string $theaterPhone): static
    {
        $this->theaterPhone = $theaterPhone;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getCompanySiret(): ?string
    {
        return $this->companySiret;
    }

    public function setCompanySiret(?string $companySiret): static
    {
        $this->companySiret = $companySiret;

        return $this;
    }

    public function getCompanyApe(): ?string
    {
        return $this->companyApe;
    }

    public function setCompanyApe(?string $companyApe): static
    {
        $this->companyApe = $companyApe;

        return $this;
    }

    public function getCompanyLicense(): ?string
    {
        return $this->companyLicense;
    }

    public function setCompanyLicense(?string $companyLicense): static
    {
        $this->companyLicense = $companyLicense;

        return $this;
    }

    public function getCompanyPresident(): ?string
    {
        return $this->companyPresident;
    }

    public function setCompanyPresident(?string $companyPresident): static
    {
        $this->companyPresident = $companyPresident;

        return $this;
    }

    public function getCompanyAddress(): ?string
    {
        return $this->companyAddress;
    }

    public function setCompanyAddress(?string $companyAddress): static
    {
        $this->companyAddress = $companyAddress;

        return $this;
    }

    public function getCompanyAssurance(): ?string
    {
        return $this->companyAssurance;
    }

    public function setCompanyAssurance(?string $companyAssurance): static
    {
        $this->companyAssurance = $companyAssurance;

        return $this;
    }

    public function getCompanyPhone(): ?string
    {
        return $this->companyPhone;
    }

    public function setCompanyPhone(?string $companyPhone): static
    {
        $this->companyPhone = $companyPhone;

        return $this;
    }

    public function getShowName(): ?string
    {
        return $this->showName;
    }

    public function setShowName(?string $showName): static
    {
        $this->showName = $showName;

        return $this;
    }

    public function getShowAuthor(): ?string
    {
        return $this->showAuthor;
    }

    public function setShowAuthor(?string $showAuthor): static
    {
        $this->showAuthor = $showAuthor;

        return $this;
    }

    public function getShowDirector(): ?string
    {
        return $this->showDirector;
    }

    public function setShowDirector(?string $showDirector): static
    {
        $this->showDirector = $showDirector;

        return $this;
    }

    public function getShowArtistCount(): ?int
    {
        return $this->showArtistCount;
    }

    public function setShowArtistCount(?int $showArtistCount): static
    {
        $this->showArtistCount = $showArtistCount;

        return $this;
    }

    public function getShowDuration(): ?int
    {
        return $this->showDuration;
    }

    public function setShowDuration(?int $showDuration): static
    {
        $this->showDuration = $showDuration;

        return $this;
    }

    public function getShowTheaterAvailability(): ?string
    {
        return $this->showTheaterAvailability;
    }

    public function setShowTheaterAvailability(?string $showTheaterAvailability): static
    {
        $this->showTheaterAvailability = $showTheaterAvailability;

        return $this;
    }

    public function getTheaterBookingPhone(): ?string
    {
        return $this->theaterBookingPhone;
    }

    public function setTheaterBookingPhone(?string $theaterBookingPhone): static
    {
        $this->theaterBookingPhone = $theaterBookingPhone;

        return $this;
    }

    public function getShowFullPrice(): ?string
    {
        return $this->showFullPrice;
    }

    public function setShowFullPrice(?string $showFullPrice): static
    {
        $this->showFullPrice = $showFullPrice;

        return $this;
    }

    public function getShowHalfPrice(): ?string
    {
        return $this->showHalfPrice;
    }

    public function setShowHalfPrice(?string $showHalfPrice): static
    {
        $this->showHalfPrice = $showHalfPrice;

        return $this;
    }

    public function getShowMaxDuration(): ?int
    {
        return $this->showMaxDuration;
    }

    public function setShowMaxDuration(?int $showMaxDuration): static
    {
        $this->showMaxDuration = $showMaxDuration;

        return $this;
    }

    public function getShowInvitations(): ?string
    {
        return $this->showInvitations;
    }

    public function setShowInvitations(?string $showInvitations): static
    {
        $this->showInvitations = $showInvitations;

        return $this;
    }

    public function getShowTheaterShare(): ?string
    {
        return $this->showTheaterShare;
    }

    public function setShowTheaterShare(?string $showTheaterShare): static
    {
        $this->showTheaterShare = $showTheaterShare;

        return $this;
    }

    public function getShowCompanyShare(): ?string
    {
        return $this->showCompanyShare;
    }

    public function setShowCompanyShare(?string $showCompanyShare): static
    {
        $this->showCompanyShare = $showCompanyShare;

        return $this;
    }

    public function getShowCompanySharePercent(): ?string
    {
        return $this->showCompanySharePercent;
    }

    public function setShowCompanySharePercent(?string $showCompanySharePercent): static
    {
        $this->showCompanySharePercent = $showCompanySharePercent;

        return $this;
    }

    public function getShowTheaterSharePercent(): ?string
    {
        return $this->showTheaterSharePercent;
    }

    public function setShowTheaterSharePercent(?string $showTheaterSharePercent): static
    {
        $this->showTheaterSharePercent = $showTheaterSharePercent;

        return $this;
    }

    public function getShowMinimumShare(): ?string
    {
        return $this->showMinimumShare;
    }

    public function setShowMinimumShare(?string $showMinimumShare): static
    {
        $this->showMinimumShare = $showMinimumShare;

        return $this;
    }

    public function getShowServiceSession(): ?string
    {
        return $this->showServiceSession;
    }

    public function setShowServiceSession(?string $showServiceSession): static
    {
        $this->showServiceSession = $showServiceSession;

        return $this;
    }

    public function getShowRib(): ?string
    {
        return $this->showRib;
    }

    public function setShowRib(?string $showRib): static
    {
        $this->showRib = $showRib;

        return $this;
    }

    public function getContractCity(): ?string
    {
        return $this->contractCity;
    }

    public function setContractCity(?string $contractCity): static
    {
        $this->contractCity = $contractCity;

        return $this;
    }

    public function getContractDate(): ?\DateTimeInterface
    {
        return $this->contractDate;
    }

    public function setContractDate(\DateTimeInterface $contractDate): static
    {
        $this->contractDate = $contractDate;

        return $this;
    }

    public function getContractSignatureDate(): ?\DateTimeInterface
    {
        return $this->contractSignatureDate;
    }

    public function setContractSignatureDate(\DateTimeInterface $contractSignatureDate): static
    {
        $this->contractSignatureDate = $contractSignatureDate;

        return $this;
    }

    public function getTva(): ?string
    {
        return $this->tva;
    }

    public function setTva(?string $tva): static
    {
        $this->tva = $tva;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Performance>
     */
    public function getPerformances(): Collection
    {
        return $this->performances;
    }

    public function addPerformance(Performance $performance): static
    {
        if (!$this->performances->contains($performance)) {
            $this->performances->add($performance);
            $performance->setContract($this);
        }

        return $this;
    }

    public function removePerformance(Performance $performance): static
    {
        if ($this->performances->removeElement($performance)) {
            // set the owning side to null (unless already changed)
            if ($performance->getContract() === $this) {
                $performance->setContract(null);
            }
        }

        return $this;
    }

    public function getRelatedProject(): ?Project
    {
        return $this->relatedProject;
    }

    public function setRelatedProject(?Project $relatedProject): static
    {
        $this->relatedProject = $relatedProject;

        return $this;
    }

    public function __toString()
    {
        return sprintf('Contrat n°%s du projet %s', $this->id, $this->relatedProject->getName());
    }
}
