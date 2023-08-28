<?php

namespace App\Entity;

use App\Repository\JobItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobItemRepository::class)]
class JobItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(targetEntity: Job::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $job;

    #[ORM\ManyToMany(targetEntity: ProContact::class, mappedBy: 'jobs')]
    private $proContacts;

    public function __construct()
    {
        $this->proContacts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return Collection<int, ProContact>
     */
    public function getProContacts(): Collection
    {
        return $this->proContacts;
    }

    public function addProContact(ProContact $proContact): self
    {
        if (!$this->proContacts->contains($proContact)) {
            $this->proContacts[] = $proContact;
            $proContact->addJob($this);
        }

        return $this;
    }

    public function removeProContact(ProContact $proContact): self
    {
        if ($this->proContacts->removeElement($proContact)) {
            $proContact->removeJob($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->job->__toString();
    }
}
