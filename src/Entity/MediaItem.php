<?php

namespace App\Entity;

use App\Repository\MediaItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaItemRepository::class)]
class MediaItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 150)]
    private $name;

    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ['all'])]
    #[ORM\JoinColumn(nullable: false)]
    private $media;

    #[ORM\ManyToOne(targetEntity: Show::class, inversedBy: 'featuredDocuments')]
    private $featuringShow;

    #[ORM\Column(type: 'integer')]
    private $position;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getFeaturingShow(): ?Show
    {
        return $this->featuringShow;
    }

    public function setFeaturingShow(?Show $featuringShow): self
    {
        $this->featuringShow = $featuringShow;

        return $this;
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
}
