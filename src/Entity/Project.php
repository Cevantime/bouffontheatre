<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['project' => 'Project', 'show' => 'Show', 'service' => 'Service'])]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ['all'])]
    private $banner;

    #[ORM\OneToOne(targetEntity: MediaGallery::class, cascade: ['persist', 'remove'])]
    private $gallery;

    #[ORM\OneToMany(targetEntity: RoleItem::class, mappedBy: 'project', orphanRemoval: true, cascade: ['all'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private $roles;

    #[ORM\OneToMany(targetEntity: Offer::class, mappedBy: 'project')]
    private $offers;

    /**
     * @Gedmo\Slug(fields={"name"})
     */
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $slug;

    #[ORM\OneToMany(targetEntity: View::class, mappedBy: 'project')]
    private $views;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ownedProjects')]
    #[ORM\JoinColumn(nullable: false)]
    private $owner;

    #[ORM\OneToMany(mappedBy: 'relatedProject', targetEntity: Performance::class)]
    private Collection $performances;

    #[ORM\OneToMany(mappedBy: 'relatedProject', targetEntity: Contract::class)]
    private Collection $contracts;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->offers = new ArrayCollection();
        $this->views = new ArrayCollection();
        $this->performances = new ArrayCollection();
        $this->contracts = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }


    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int, ArtistItem>
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }

    public function addActor(ArtistItem $actor): self
    {
        if (!$this->actors->contains($actor)) {
            $this->actors[] = $actor;
            $actor->setActedProject($this);
        }

        return $this;
    }

    public function removeActor(ArtistItem $actor): self
    {
        if ($this->actors->removeElement($actor)) {
            // set the owning side to null (unless already changed)
            if ($actor->getActedProject() === $this) {
                $actor->setActedProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ArtistItem>
     */
    public function getDirectors(): Collection
    {
        return $this->directors;
    }

    public function addDirector(ArtistItem $director): self
    {
        if (!$this->directors->contains($director)) {
            $this->directors[] = $director;
            $director->setDirectedProject($this);
        }

        return $this;
    }

    public function removeDirector(ArtistItem $director): self
    {
        if ($this->directors->removeElement($director)) {
            // set the owning side to null (unless already changed)
            if ($director->getDirectedProject() === $this) {
                $director->setDirectedProject(null);
            }
        }

        return $this;
    }

    public function getBanner(): ?Media
    {
        return $this->banner;
    }

    public function setBanner(?Media $banner): self
    {
        $this->banner = $banner;

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

    /**
     * @return Collection<int, RoleItem>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(RoleItem $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
            $role->setProject($this);
        }

        return $this;
    }

    public function removeRole(RoleItem $role): self
    {
        if ($this->roles->removeElement($role)) {
            // set the owning side to null (unless already changed)
            if ($role->getProject() === $this) {
                $role->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Offer>
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): self
    {
        if (!$this->offers->contains($offer)) {
            $this->offers[] = $offer;
            $offer->setProject($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): self
    {
        if ($this->offers->removeElement($offer)) {
            // set the owning side to null (unless already changed)
            if ($offer->getProject() === $this) {
                $offer->setProject(null);
            }
        }

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
     * @return Collection<int, View>
     */
    public function getViews(): Collection
    {
        return $this->views;
    }

    public function addView(View $view): self
    {
        if (!$this->views->contains($view)) {
            $this->views[] = $view;
            $view->setProject($this);
        }

        return $this;
    }

    public function removeView(View $view): self
    {
        if ($this->views->removeElement($view)) {
            // set the owning side to null (unless already changed)
            if ($view->getProject() === $this) {
                $view->setProject(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Performance>
     */
    public function getPerformances(): Collection
    {
        return $this->performances;
    }

    public function addPerformance(Performance $performance): static
    {
        if (!$this->performances->contains($performance)) {
            $this->performances->add($performance);
            $performance->setRelatedProject($this);
        }

        return $this;
    }

    public function removePerformance(Performance $performance): static
    {
        if ($this->performances->removeElement($performance)) {
            // set the owning side to null (unless already changed)
            if ($performance->getRelatedProject() === $this) {
                $performance->setRelatedProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contract>
     */
    public function getContracts(): Collection
    {
        return $this->contracts;
    }

    public function addContract(Contract $contract): static
    {
        if (!$this->contracts->contains($contract)) {
            $this->contracts->add($contract);
            $contract->setRelatedProject($this);
        }

        return $this;
    }

    public function removeContract(Contract $contract): static
    {
        if ($this->contracts->removeElement($contract)) {
            // set the owning side to null (unless already changed)
            if ($contract->getRelatedProject() === $this) {
                $contract->setRelatedProject(null);
            }
        }

        return $this;
    }
}
