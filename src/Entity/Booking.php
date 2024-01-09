<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Index(name: "google_id_idx", columns: ["google_id"])]
class Booking
{
    public const TYPE_CONCERT = 'type.concert';
    public const TYPE_SHOW = 'type.show';
    public const TYPE_REPETITION = 'type.rehearsal';
    public const TYPE_COURSE = 'type.course';
    public const TYPE_APPOINTMENT = 'type.appointment';
    public const TYPE_DEFAULT = 'type.default';

    public const TYPES = [
        self::TYPE_DEFAULT,
        self::TYPE_CONCERT,
        self::TYPE_SHOW,
        self::TYPE_REPETITION,
        self::TYPE_COURSE,
        self::TYPE_APPOINTMENT
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private $beginAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\GreaterThan(propertyPath: 'beginAt')]
    private $endAt;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'string', length: 30)]
    private $type;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $googleId = null;

    #[ORM\Column]
    private ?bool $isInstance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBeginAt(): ?\DateTimeImmutable
    {
        return $this->beginAt;
    }

    public function setBeginAt(\DateTimeImmutable $beginAt): self
    {
        $this->beginAt = $beginAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): static
    {
        $this->googleId = $googleId;

        return $this;
    }

    public function isInstance(): ?bool
    {
        return $this->isInstance;
    }

    public function setIsInstance(bool $isInstance): static
    {
        $this->isInstance = $isInstance;

        return $this;
    }
}
