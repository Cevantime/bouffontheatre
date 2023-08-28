<?php

namespace App\Entity;

use App\Repository\ArtistItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistItemRepository::class)]
class ArtistItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $position;

    #[ORM\ManyToOne(targetEntity: Artist::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $artist;

    #[ORM\ManyToOne(targetEntity: Show::class, inversedBy: 'actors')]
    private $actedProject;

    #[ORM\ManyToOne(targetEntity: Show::class, inversedBy: 'directors')]
    private $directedProject;

    #[ORM\ManyToOne(targetEntity: Show::class, inversedBy: 'authors')]
    private $authoredShow;

    #[ORM\ManyToOne(targetEntity: Content::class, inversedBy: 'artistGallery')]
    private $content;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getActedProject(): ?Project
    {
        return $this->actedProject;
    }

    public function setActedProject(?Project $actedProject): self
    {
        $this->actedProject = $actedProject;

        return $this;
    }

    public function getDirectedProject(): ?Project
    {
        return $this->directedProject;
    }

    public function setDirectedProject(?Project $directedProject): self
    {
        $this->directedProject = $directedProject;

        return $this;
    }

    public function __toString(): string
    {
        return $this->artist->__toString();
    }

    public function getAuthoredShow(): ?Show
    {
        return $this->authoredShow;
    }

    public function setAuthoredShow(?Show $authoredShow): self
    {
        $this->authoredShow = $authoredShow;

        return $this;
    }

    public function getContent(): ?Content
    {
        return $this->content;
    }

    public function setContent(?Content $content): self
    {
        $this->content = $content;

        return $this;
    }
}
