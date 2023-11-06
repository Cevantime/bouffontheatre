<?php

namespace App\Entity;

use App\Repository\ContentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContentRepository::class)]
class Content
{
    const TYPE_TITLE = 'title';
    const TYPE_TEXTE = 'text';
    const TYPE_IMAGE = 'image';
    const TYPE_PROJECT_GALLERY = 'projectGallery';

    const TYPES = [
        'Titre' => 'title',
        'Texte' => 'text',
        'Image' => 'image',
        'Gallerie de projets' => 'projectGallery',
        'Gallerie d\'article de blog' => "blogPostGallery"
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $type;

    #[ORM\Column(type: 'string', length: 150)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $help;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private $text;

    #[ORM\Column(type: 'string', length: 150, unique: true)]
    private $slug;

    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ['all'])]
    private $image;

    #[ORM\OneToMany(targetEntity: ProjectItem::class, mappedBy: 'content', cascade: ['all'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private $projectGallery;

    #[ORM\OneToMany(mappedBy: 'content', targetEntity: BlogPostItem::class, cascade: ['all'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $blogPostGallery;

    public function __construct()
    {
        $this->projectGallery = new ArrayCollection();
        $this->blogPostGallery = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function setHelp(?string $help): self
    {
        $this->help = $help;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

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

    public function getImage(): ?Media
    {
        return $this->image;
    }

    public function setImage(?Media $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @return Collection<int, ProjectItem>
     */
    public function getProjectGallery(): Collection
    {
        return $this->projectGallery;
    }

    public function addProjectGallery(ProjectItem $projectGallery): self
    {
        if (!$this->projectGallery->contains($projectGallery)) {
            $this->projectGallery[] = $projectGallery;
            $projectGallery->setContent($this);
        }

        return $this;
    }

    public function removeProjectGallery(ProjectItem $projectGallery): self
    {
        if ($this->projectGallery->removeElement($projectGallery)) {
            // set the owning side to null (unless already changed)
            if ($projectGallery->getContent() === $this) {
                $projectGallery->setContent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlogPostItem>
     */
    public function getBlogPostGallery(): Collection
    {
        return $this->blogPostGallery;
    }

    public function addBlogPostGallery(BlogPostItem $blogPostGallery): static
    {
        if (!$this->blogPostGallery->contains($blogPostGallery)) {
            $this->blogPostGallery->add($blogPostGallery);
            $blogPostGallery->setContent($this);
        }

        return $this;
    }

    public function removeBlogPostGallery(BlogPostItem $blogPostGallery): static
    {
        if ($this->blogPostGallery->removeElement($blogPostGallery)) {
            // set the owning side to null (unless already changed)
            if ($blogPostGallery->getContent() === $this) {
                $blogPostGallery->setContent(null);
            }
        }

        return $this;
    }
}
