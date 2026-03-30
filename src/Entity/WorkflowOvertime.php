<?php

namespace App\Entity;

use App\Repository\WorkflowOvertimeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkflowOvertimeRepository::class)]
class WorkflowOvertime
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\ManyToOne(inversedBy: 'overtimes')]
	#[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
	private ?Workflow $workflow = null;

	#[ORM\Column]
	private ?\DateTimeImmutable $workedAt = null;

	#[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
	private ?string $hourCount = null;

	#[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
	private ?string $unitHourPrice = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getWorkflow(): ?Workflow
	{
		return $this->workflow;
	}

	public function setWorkflow(?Workflow $workflow): static
	{
		$this->workflow = $workflow;

		return $this;
	}

	public function getWorkedAt(): ?\DateTimeImmutable
	{
		return $this->workedAt;
	}

	public function setWorkedAt(\DateTimeImmutable $workedAt): static
	{
		$this->workedAt = $workedAt;

		return $this;
	}

	public function getHourCount(): ?string
	{
		return $this->hourCount;
	}

	public function setHourCount(string $hourCount): static
	{
		$this->hourCount = $hourCount;

		return $this;
	}

	public function getUnitHourPrice(): ?string
	{
		return $this->unitHourPrice;
	}

	public function setUnitHourPrice(string $unitHourPrice): static
	{
		$this->unitHourPrice = $unitHourPrice;

		return $this;
	}

	public function getTotalPrice(): ?string
	{
		if ($this->hourCount === null || $this->unitHourPrice === null) {
			return null;
		}

		return bcmul($this->hourCount, $this->unitHourPrice, 2);
	}

	public function __toString(): string
	{
		if ($this->workedAt === null) {
			return 'Heure supplémentaire';
		}

		return sprintf('Heures sup du %s', $this->workedAt->format('d/m/Y'));
	}
}
