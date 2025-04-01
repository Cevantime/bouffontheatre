<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\ShowRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShowRepository::class)]
class Show extends Project
{
    #[ORM\OneToMany(targetEntity: ArtistItem::class, mappedBy: 'actedProject', cascade: ['all'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private $actors;

    #[ORM\OneToMany(targetEntity: ArtistItem::class, mappedBy: 'directedProject', cascade: ['all'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private $directors;

    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ['all'])]
    private $poster;

    #[ORM\OneToMany(targetEntity: LinkItem::class, mappedBy: 'shopLinkedShow', cascade: ['all'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private $shopLinks;

    #[ORM\OneToMany(targetEntity: PaperItem::class, mappedBy: 'project', cascade: ['all'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private $papers;

    #[ORM\OneToMany(targetEntity: PeriodItem::class, mappedBy: 'project', cascade: ['all'])]
    private $sessions;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $punchline;

    #[ORM\OneToMany(targetEntity: ArtistItem::class, mappedBy: 'authoredShow', cascade: ['all'])]
    private $authors;

    #[ORM\OneToMany(targetEntity: MediaItem::class, mappedBy: 'featuringShow', cascade: ['all'])]
    private $featuredDocuments;

    #[ORM\OneToMany(targetEntity: LinkItem::class, mappedBy: 'featuringShow', cascade: ['all'])]
    private $featuredLinks;

    #[ORM\Column]
    private ?bool $bookable = false;

    #[ORM\OneToMany(mappedBy: 'relatedShow', targetEntity: Insight::class)]
    private Collection $insights;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $billetreducTitle = null;

    #[ORM\Column]
    private bool $bookableOnline = false;

    /**
     * @var Collection<int, Workflow>
     */
    #[ORM\OneToMany(mappedBy: 'associatedShow', targetEntity: Workflow::class)]
    private Collection $workflows;

    public function __construct()
    {
        parent::__construct();
        $this->sessions = new ArrayCollection();
        $this->papers = new ArrayCollection();
        $this->shopLinks = new ArrayCollection();
        $this->actors = new ArrayCollection();
        $this->directors = new ArrayCollection();
        $this->authors = new ArrayCollection();
        $this->featuredDocuments = new ArrayCollection();
        $this->featuredLinks = new ArrayCollection();
        $this->insights = new ArrayCollection();
        $this->workflows = new ArrayCollection();
    }

    public function canBeBooked(): bool
    {
        return $this->isBookable() && ! $this->shopLinks->isEmpty() && $this->getLastSession() && $this->getLastSession()->getPeriod()->getDateEnd()->format('U') > \time();
    }

    public function getLastSession(): ?PeriodItem
    {
        $sessionsAsArray = $this->sessions->toArray();
        usort($sessionsAsArray, function (PeriodItem $periodItem1, PeriodItem $periodItem2) {
            return $periodItem1->getPeriod()->getDateEnd() <=> $periodItem2->getPeriod()->getDateEnd();
        });
        return $sessionsAsArray ? $sessionsAsArray[count($sessionsAsArray) - 1] : null;
    }

    /**
     * @return Collection<int, LinkItem>
     */
    public function getShopLinks(): Collection
    {
        return $this->shopLinks;
    }

    public function addShopLink(LinkItem $shopLink): self
    {
        if (!$this->shopLinks->contains($shopLink)) {
            $this->shopLinks[] = $shopLink;
            $shopLink->setShopLinkedShow($this);
        }

        return $this;
    }

    public function removeShopLink(LinkItem $shopLink): self
    {
        if ($this->shopLinks->removeElement($shopLink)) {
            // set the owning side to null (unless already changed)
            if ($shopLink->getShopLinkedShow() === $this) {
                $shopLink->setShopLinkedShow(null);
            }
        }

        return $this;
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

    public function getPoster(): ?Media
    {
        return $this->poster;
    }

    public function setPoster(?Media $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    /**
     * @return Collection<int, PaperItem>
     */
    public function getPapers(): Collection
    {
        return $this->papers;
    }

    public function addPaper(PaperItem $paper): self
    {
        if (!$this->papers->contains($paper)) {
            $this->papers[] = $paper;
            $paper->setProject($this);
        }

        return $this;
    }

    public function removePaper(PaperItem $paper): self
    {
        if ($this->papers->removeElement($paper)) {
            // set the owning side to null (unless already changed)
            if ($paper->getProject() === $this) {
                $paper->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PeriodItem>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(PeriodItem $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->setProject($this);
        }

        return $this;
    }

    public function removeSession(PeriodItem $session): self
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getProject() === $this) {
                $session->setProject(null);
            }
        }

        return $this;
    }

    public function getCurrentSessions()
    {
        return $this->sessions->filter(function (PeriodItem $periodItem) {
            return $periodItem->getPeriod()->getDateEnd() > new \DateTime();
        });
    }

    public function getPunchline(): ?string
    {
        return $this->punchline;
    }

    public function setPunchline(?string $punchline): self
    {
        $this->punchline = $punchline;

        return $this;
    }

    /**
     * @return Collection<int, ArtistItem>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(ArtistItem $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
            $author->setAuthoredShow($this);
        }

        return $this;
    }

    public function removeAuthor(ArtistItem $author): self
    {
        if ($this->authors->removeElement($author)) {
            // set the owning side to null (unless already changed)
            if ($author->getAuthoredShow() === $this) {
                $author->setAuthoredShow(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MediaItem>
     */
    public function getFeaturedDocuments(): Collection
    {
        return $this->featuredDocuments;
    }

    public function addFeaturedDocument(MediaItem $featuredDocument): self
    {
        if (!$this->featuredDocuments->contains($featuredDocument)) {
            $this->featuredDocuments[] = $featuredDocument;
            $featuredDocument->setFeaturingShow($this);
        }

        return $this;
    }

    public function removeFeaturedDocument(MediaItem $featuredDocument): self
    {
        if ($this->featuredDocuments->removeElement($featuredDocument)) {
            // set the owning side to null (unless already changed)
            if ($featuredDocument->getFeaturingShow() === $this) {
                $featuredDocument->setFeaturingShow(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LinkItem>
     */
    public function getFeaturedLinks(): Collection
    {
        return $this->featuredLinks;
    }

    public function addFeaturedLink(LinkItem $featuredLink): self
    {
        if (!$this->featuredLinks->contains($featuredLink)) {
            $this->featuredLinks[] = $featuredLink;
            $featuredLink->setFeaturingShow($this);
        }

        return $this;
    }

    public function removeFeaturedLink(LinkItem $featuredLink): self
    {
        if ($this->featuredLinks->removeElement($featuredLink)) {
            // set the owning side to null (unless already changed)
            if ($featuredLink->getFeaturingShow() === $this) {
                $featuredLink->setFeaturingShow(null);
            }
        }

        return $this;
    }

    public function isBookable(): ?bool
    {
        return $this->bookable;
    }

    public function setBookable(bool $bookable): static
    {
        $this->bookable = $bookable;

        return $this;
    }

    /**
     * @return Collection<int, Insight>
     */
    public function getInsights(): Collection
    {
        return $this->insights;
    }

    public function addInsight(Insight $insight): static
    {
        if (!$this->insights->contains($insight)) {
            $this->insights->add($insight);
            $insight->setRelatedShow($this);
        }

        return $this;
    }

    public function removeInsight(Insight $insight): static
    {
        if ($this->insights->removeElement($insight)) {
            // set the owning side to null (unless already changed)
            if ($insight->getRelatedShow() === $this) {
                $insight->setRelatedShow(null);
            }
        }

        return $this;
    }

    public function getBilletreducTitle(): ?string
    {
        return $this->billetreducTitle;
    }

    public function setBilletreducTitle(?string $billetreducTitle): static
    {
        $this->billetreducTitle = $billetreducTitle;

        return $this;
    }

    public function isBookableOnline(): bool
    {
        return $this->bookableOnline;
    }

    public function setBookableOnline(bool $bookableOnline): static
    {
        $this->bookableOnline = $bookableOnline;

        return $this;
    }

    /**
     * @return Collection<int, Workflow>
     */
    public function getWorkflows(): Collection
    {
        return $this->workflows;
    }

    public function addWorkflow(Workflow $workflow): static
    {
        if (!$this->workflows->contains($workflow)) {
            $this->workflows->add($workflow);
            $workflow->setAssociatedShow($this);
        }

        return $this;
    }

    public function removeWorkflow(Workflow $workflow): static
    {
        if ($this->workflows->removeElement($workflow)) {
            // set the owning side to null (unless already changed)
            if ($workflow->getAssociatedShow() === $this) {
                $workflow->setAssociatedShow(null);
            }
        }

        return $this;
    }
}
