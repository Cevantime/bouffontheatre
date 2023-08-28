<?php

namespace App\Entity;

use App\Repository\PeriodRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PeriodRepository::class)]
class Period
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date')]
    private $dateStart;

    #[ORM\Column(type: 'date')]
    private $dateEnd;

    #[ORM\Column(type: 'simple_array')]
    private $days = [];

    public const DAYS = [
        'Lundi' => 0,
        'Mardi' => 1,
        'Mercredi' => 2,
        'Jeudi' => 3,
        'Vendredi' => 4,
        'Samedi' => 5,
        'Dimanche' => 6,
    ];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getDays(): ?array
    {
        return $this->days;
    }

    public function setDays(array $days): self
    {
        $this->days = $days;

        return $this;
    }

    public function __toString(): string
    {
        return 'du '.$this->getDateStart()->format('d/m/Y').' au '.$this->getDateEnd()->format('d/m/Y');
    }
}
