<?php

namespace App\Entity;

use App\Repository\ProContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProContactRepository::class)]
class ProContact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $lastname;

    #[ORM\Column(type: 'string', length: 60, nullable: true)]
    private $stageName;

    #[ORM\ManyToMany(targetEntity: JobItem::class, inversedBy: 'proContacts', cascade: ['all'])]
    private $jobs;

    public function __construct()
    {
        $this->jobs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
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

    /**
     * @return Collection<int, JobItem>
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(JobItem $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs[] = $job;
        }

        return $this;
    }

    public function removeJob(JobItem $job): self
    {
        $this->jobs->removeElement($job);

        return $this;
    }

    public function getFullName()
    {
        return $this->firstname . ' '.$this->lastname;
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }
}
