<?php

namespace App\Entity;

use App\Repository\PaperRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaperRepository::class)]
class Paper
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $extract;

    #[ORM\OneToOne(targetEntity: Link::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $link;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExtract(): ?string
    {
        return $this->extract;
    }

    public function setExtract(string $extract): self
    {
        $this->extract = $extract;

        return $this;
    }

    public function getLink(): ?Link
    {
        return $this->link;
    }

    public function setLink(Link $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getLink()->getTitle();
    }
}
