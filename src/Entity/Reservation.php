<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use App\Validator\Reservation as ValidatorReservation;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Positive;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ValidatorReservation()]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Performance $performance = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[Positive()]
    #[ORM\Column(nullable: true)]
    private ?int $tarif1 = null;

    #[Positive()]
    #[ORM\Column(nullable: true)]
    private ?int $tarif2 = null;

    #[Positive()]
    #[ORM\Column(nullable: true)]
    private ?int $tarif3 = null;

    #[Positive()]
    #[ORM\Column(nullable: true)]
    private ?int $tarif4 = null;

    #[ORM\Column(length: 60)]
    private ?string $firstName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerformance(): ?Performance
    {
        return $this->performance;
    }

    public function setPerformance(?Performance $performance): static
    {
        $this->performance = $performance;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getTarif1(): ?int
    {
        return $this->tarif1;
    }

    public function setTarif1(?int $tarif1): static
    {
        $this->tarif1 = $tarif1;

        return $this;
    }

    public function getTarif2(): ?int
    {
        return $this->tarif2;
    }

    public function setTarif2(?int $tarif2): static
    {
        $this->tarif2 = $tarif2;

        return $this;
    }

    public function getTarif3(): ?int
    {
        return $this->tarif3;
    }

    public function setTarif3(?int $tarif3): static
    {
        $this->tarif3 = $tarif3;

        return $this;
    }

    public function getTarif4(): ?int
    {
        return $this->tarif4;
    }

    public function setTarif4(?int $tarif4): static
    {
        $this->tarif4 = $tarif4;

        return $this;
    }

    public function getSumTarifs()
    {
        return $this->tarif1 + $this->tarif2 + $this->tarif3 + $this->tarif4;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function __toString(): string
    {
        return $this->firstName .' '.$this->lastName;
    }
}
