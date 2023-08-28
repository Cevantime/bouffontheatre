<?php

namespace App\Entity;

use App\Repository\LinkItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkItemRepository::class)]
class LinkItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $position;

    #[ORM\OneToOne(targetEntity: Link::class, cascade: ['all'])]
    #[ORM\JoinColumn(nullable: false)]
    private $link;

    #[ORM\ManyToOne(targetEntity: Show::class, inversedBy: 'shopLinks')]
    private $shopLinkedShow;

    #[ORM\ManyToOne(targetEntity: Show::class, inversedBy: 'featuredLinks')]
    private $featuringShow;

    #[ORM\ManyToOne(targetEntity: Artist::class, inversedBy: 'links')]
    private $artist;

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

    public function getLink(): ?Link
    {
        return $this->link;
    }

    public function setLink(?Link $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getShopLinkedShow(): ?Project
    {
        return $this->shopLinkedShow;
    }

    public function setShopLinkedShow(?Project $shopLinkedShow): self
    {
        $this->shopLinkedShow = $shopLinkedShow;

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

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): self
    {
        $this->artist = $artist;

        return $this;
    }
}
