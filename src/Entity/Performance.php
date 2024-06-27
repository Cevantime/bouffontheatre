<?php

namespace App\Entity;

use App\Repository\PerformanceRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(mappedBy: 'performance', targetEntity: Reservation::class)]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setPerformance($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getPerformance() === $this) {
                $reservation->setPerformance(null);
            }
        }

        return $this;
    }

    public function getReservationsSum(?Reservation $ignoreReservation = null)
    {
        return $this->reservations->reduce(fn ($acc, $r) => $acc + ($ignoreReservation == $r ? 0 : $r->getSumTarifs()));
    }

    public function isAvailableForReservation()
    {
        return $this->getAvailableReservationsSum() > 0 && !$this->hasExpired();
    }

    public function hasExpired()
    {
        $today = new DateTime();
        $today->modify('+1 hours');
        return $this->performedAt < $today;
    }

    public function getAvailableReservationsSum(?Reservation $ignoreReservation = null)
    {
        return 45 - $this->getReservationsSum($ignoreReservation);
    }

    public function __toString()
    {
        return $this->relatedProject->getName() . ' ' . $this->getPerformedAt()->format('d/m/Y H:i');
    }
}
