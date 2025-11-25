<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use App\Validator\Reservation as ValidatorReservation;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Positive;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ValidatorReservation()]
class Reservation
{

    const AVAILABLE_PRICES = [
        'none' => 'Non précisé',
        'plain' => 'Plein Tarif',
        'discount' => 'Tarif Réduit',
        'group' => 'Groupe',
        'enfant' => 'Enfant',
        'invitation' => 'Invitation',
    ];
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
    private ?int $placeCount = 0;

    #[ORM\Column(length: 60)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email()]
    private ?string $email = null;

    #[ORM\Column(length: 60)]
    private ?string $price = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne]
    private ?User $author = null;

    #[ORM\Column]
    private ?bool $checked = false;

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

    public function getPlaceCount(): ?int
    {
        return $this->placeCount;
    }

    public function setPlaceCount(?int $placeCount): static
    {
        $this->placeCount = $placeCount;

        return $this;
    }

    public function getSumTarifs()
    {
        return $this->placeCount;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceLabel(): string
    {
        return self::AVAILABLE_PRICES[$this->getPrice()] ?? '';
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function isChecked(): ?bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): static
    {
        $this->checked = $checked;

        return $this;
    }
}
