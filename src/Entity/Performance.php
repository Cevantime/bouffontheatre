<?php

namespace App\Entity;

use App\Repository\PerformanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PerformanceRepository::class)]
class Performance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $performedAt = null;

    #[ORM\ManyToOne(inversedBy: 'performances')]
    private ?Project $relatedProject = null;

    #[ORM\ManyToOne(inversedBy: 'performances')]
    private ?Contract $contract = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerformedAt(): ?\DateTimeImmutable
    {
        return $this->performedAt;
    }

    public function setPerformedAt(\DateTimeImmutable $performedAt): static
    {
        $this->performedAt = $performedAt;

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

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): static
    {
        $this->contract = $contract;

        return $this;
    }
}
