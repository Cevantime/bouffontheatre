<?php

namespace App\Entity;

use App\Repository\WorkflowRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\Timestampable;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: WorkflowRepository::class)]
class Workflow
{
    const STEP_CONTRACT_CREATION = 'contract_creation';
    const STEP_FETCH_INFORMATIONS = 'fetch_informations';
    const STEP_SEND_CONTRACT = 'send_contract';
    const STEP_SIGN_CONTRACT = 'sign_contract';
    const STEP_BILLETREDUC = 'billetreduc';
    const STEP_HIGHTLIGHT = 'highlight';
    const STEP_REMOVE = 'removed';
    const STEP_REVENUE_DECLARATION = 'revenue_declaration';
    const STEP_EMAILS = 'emails';
    const STEP_SIBIL = 'sibil';
    const STEP_DECTANET = 'dectanet';
    const STEP_MANUAL = 'manual';

    const STEPS = [
        self::STEP_CONTRACT_CREATION,
        self::STEP_FETCH_INFORMATIONS,
        self::STEP_SEND_CONTRACT,
        self::STEP_SIGN_CONTRACT,
        self::STEP_BILLETREDUC,
        self::STEP_HIGHTLIGHT,
        self::STEP_REMOVE,
        self::STEP_REVENUE_DECLARATION,
        self::STEP_EMAILS,
        self::STEP_SIBIL,
        self::STEP_DECTANET,
        self::STEP_MANUAL,
    ];

    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'workflow', cascade: ['persist'])]
    private ?Contract $contract = null;

    #[ORM\Column]
    private ?bool $showHighlighted = false;

    #[ORM\Column]
    private ?bool $showRemoved = false;

    #[ORM\Column]
    private ?bool $sibilDone = false;

    #[ORM\Column]
    private ?bool $dectanetDone = false;

    #[ORM\Column]
    private ?bool $revenueEmailSentToPresident = false;


    #[ORM\Column]
    private ?bool $revenueEmailSentToCompany = false;

    #[ORM\ManyToOne(inversedBy: 'workflows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Show $associatedShow = null;

    #[ORM\Column]
    private ?bool $closed = false;

    #[ORM\Column(nullable: true)]
    private ?bool $copyrightApplicable = null;

    #[ORM\Column(nullable: true)]
    private ?bool $retirementContribApplicable = null;

    #[ORM\Column(nullable: true)]
    private ?bool $agessaContribApplicable = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Media $revenueTickBossPdf = null;

    #[ORM\Column]
    private ?bool $manualStepsDone = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(Contract $contract): static
    {
        $this->contract = $contract;

        return $this;
    }

    public function isShowHighlighted(): ?bool
    {
        return $this->showHighlighted;
    }

    public function setShowHighlighted(bool $showHighlighted): static
    {
        $this->showHighlighted = $showHighlighted;

        return $this;
    }

    public function isShowRemoved(): ?bool
    {
        return $this->showRemoved;
    }

    public function setShowRemoved(bool $showRemoved): static
    {
        $this->showRemoved = $showRemoved;

        return $this;
    }

    public function isSibilDone(): ?bool
    {
        return $this->sibilDone;
    }

    public function setSibilDone(bool $sibilDone): static
    {
        $this->sibilDone = $sibilDone;

        return $this;
    }

    public function isDectanetDone(): ?bool
    {
        return $this->dectanetDone;
    }

    public function setDectanetDone(bool $dectanetDone): static
    {
        $this->dectanetDone = $dectanetDone;

        return $this;
    }

    public function isRevenueEmailSentToPresident(): ?bool
    {
        return $this->revenueEmailSentToPresident;
    }

    public function setRevenueEmailSentToPresident(bool $revenueEmailSentToPresident): static
    {
        $this->revenueEmailSentToPresident = $revenueEmailSentToPresident;

        return $this;
    }

    public function getContractDate(): \DateTimeInterface
    {
        return $this->contract->getContractDate();
    }

    public function getAssociatedShow(): ?Show
    {
        return $this->associatedShow;
    }

    public function setAssociatedShow(?Show $associatedShow): static
    {
        $this->associatedShow = $associatedShow;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('Contrat du %s pour le spectacle %s', $this->getCreatedAt()->format('d/m/Y'), $this->getAssociatedShow());
    }

    public function isClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): static
    {
        $this->closed = $closed;

        return $this;
    }

    public function isCopyrightApplicable(): ?bool
    {
        return $this->copyrightApplicable;
    }

    public function setCopyrightApplicable(?bool $copyrightApplicable): static
    {
        $this->copyrightApplicable = $copyrightApplicable;

        return $this;
    }

    public function isRetirementContribApplicable(): ?bool
    {
        return $this->retirementContribApplicable;
    }

    public function setRetirementContribApplicable(?bool $retirementContribApplicable): static
    {
        $this->retirementContribApplicable = $retirementContribApplicable;

        return $this;
    }

    public function isAgessaContribApplicable(): ?bool
    {
        return $this->agessaContribApplicable;
    }

    public function setAgessaContribApplicable(?bool $agessaContribApplicable): static
    {
        $this->agessaContribApplicable = $agessaContribApplicable;

        return $this;
    }

    public function getRevenueTickBossPdf(): ?Media
    {
        return $this->revenueTickBossPdf;
    }

    public function setRevenueTickBossPdf(?Media $revenueTickBossPdf): static
    {
        $this->revenueTickBossPdf = $revenueTickBossPdf;

        return $this;
    }

    public function isManualStepsDone(): ?bool
    {
        return $this->manualStepsDone;
    }

    public function setManualStepsDone(bool $manualStepsDone): static
    {
        $this->manualStepsDone = $manualStepsDone;

        return $this;
    }

    public function getAssociatedShowName(): ?string
    {
        if($this->associatedShow === null) {
            return null;
        }
        return $this->associatedShow->getName();
    }

    public function getContractName(): ?string
    {
        if($this->contract === null) {
            return null;
        }
        return $this->contract->__toString();
    }

    public function isRevenueEmailSentToCompany(): ?bool
    {
        return $this->revenueEmailSentToCompany;
    }

    public function setRevenueEmailSentToCompany(bool $revenueEmailSentToCompany): static
    {
        $this->revenueEmailSentToCompany = $revenueEmailSentToCompany;

        return $this;
    }
}
