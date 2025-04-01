<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
class Artist
{
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    const GENDERS = [
        self::GENDER_FEMALE,
        self::GENDER_MALE
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 60)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 60)]
    private $lastname;

    #[ORM\Column(type: 'string', length: 60, nullable: true)]
    private $stageName;

    #[ORM\Column(type: 'boolean')]
    private $hasFile = false;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'associatedArtist', cascade: ['persist', 'remove'])]
    private $associatedUser;

    #[ORM\Column(type: 'text', nullable: true)]
    private $biography;

    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ['all'])]
    private $photo;

    #[ORM\OneToOne(targetEntity: MediaGallery::class, inversedBy: 'artist', cascade: ['persist', 'remove'])]
    private $gallery;

    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    #[ORM\OneToMany(targetEntity: LinkItem::class, mappedBy: 'artist', cascade: ['all'])]
    private $links;

    #[ORM\ManyToMany(targetEntity: Job::class)]
    private $jobs;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $gender;

    public function __construct()
    {
        $this->links = new ArrayCollection();
        $this->jobs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getFullname()
    {
        return $this->stageName ?: $this->firstname . ' ' . $this->lastname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getStageName(): ?string
    {
        return $this->stageName;
    }

    public function setStageName(?string $stageName): self
    {
        $this->stageName = $stageName;

        return $this;
    }

    public function hasFile(): ?bool
    {
        return $this->hasFile;
    }

    public function setHasFile(bool $hasFile): self
    {
        $this->hasFile = $hasFile;

        return $this;
    }

    public function getAssociatedUser(): ?User
    {
        return $this->associatedUser;
    }

    public function setAssociatedUser(?User $associatedUser): self
    {
        $this->associatedUser = $associatedUser;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getFullname();
    }

    public function getPhoto(): ?Media
    {
        return $this->photo;
    }

    public function setPhoto(?Media $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getGallery(): ?MediaGallery
    {
        return $this->gallery;
    }

    public function setGallery(?MediaGallery $gallery): self
    {
        $this->gallery = $gallery;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, LinkItem>
     */
    public function getLinks(): Collection
    {
        return $this->links;
    }

    public function addLink(LinkItem $link): self
    {
        if (!$this->links->contains($link)) {
            $this->links[] = $link;
            $link->setArtist($this);
        }

        return $this;
    }

    public function removeLink(LinkItem $link): self
    {
        if ($this->links->removeElement($link)) {
            // set the owning side to null (unless already changed)
            if ($link->getArtist() === $this) {
                $link->setArtist(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Job>
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs[] = $job;
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        $this->jobs->removeElement($job);

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getJobNames()
    {
        return $this->jobs->map(function (Job $j) {
            if ($this->gender === null || $this->gender === self::GENDER_MALE) {
                return $j->getName();
            } elseif ($j->getFeminin() !== null) {
                return $j->getFeminin();
            }
            return $j->getName();
        });
    }
}
