<?php

namespace App\Entity;

use App\Repository\MediaGalleryRepository;
use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Entity\BaseGallery;

#[ORM\Entity(repositoryClass: MediaGalleryRepository::class)]
class MediaGallery extends BaseGallery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(targetEntity: Artist::class, mappedBy: 'gallery', cascade: ['persist', 'remove'])]
    private $artist;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): self
    {
        // unset the owning side of the relation if necessary
        if ($artist === null && $this->artist !== null) {
            $this->artist->setGallery(null);
        }

        // set the owning side of the relation if necessary
        if ($artist !== null && $artist->getGallery() !== $this) {
            $artist->setGallery($this);
        }

        $this->artist = $artist;

        return $this;
    }
}
