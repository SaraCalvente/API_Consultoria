<?php

namespace App\Project\Domain\Project;

use App\ActivityHistory\Domain\ActivityHistory;
use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant\Consultant;
use App\Project\Domain\Status;
use App\Project\Infraestructure\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column (type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $start_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $end_date = null;

    #[ORM\Column(type: 'string', length: 255, enumType: Status::class)]
    private ?Status $status = null;

    #[ORM\ManyToMany(targetEntity: Consultant::class, inversedBy: 'project')]
    private Collection $consultant;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: ActivityHistory::class)]
    private Collection $activity_history;

    public function __construct()
    {
        $this->consultant = new ArrayCollection();
        $this->activity_history = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(?\DateTimeInterface $end_date): static
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Consultant>
     */
    public function getConsultant(): Collection
    {
        return $this->consultant;
    }

    public function addConsultant(Consultant $consultant): static
    {
        if (!$this->consultant->contains($consultant)) {
            $this->consultant->add($consultant);
        }

        return $this;
    }

    public function removeConsultant(Consultant $consultant): static
    {
        $this->consultant->removeElement($consultant);

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, ActivityHistory>
     */
    public function getActivityHistory(): Collection
    {
        return $this->activity_history;
    }

    public function addActivityHistory(ActivityHistory $activityHistory): static
    {
        if (!$this->activity_history->contains($activityHistory)) {
            $this->activity_history->add($activityHistory);
            $activityHistory->setProject($this);
        }

        return $this;
    }

    public function removeActivityHistory(ActivityHistory $activityHistory): static
    {
        if ($this->activity_history->removeElement($activityHistory)) {
            // set the owning side to null (unless already changed)
            if ($activityHistory->getProject() === $this) {
                $activityHistory->setProject(null);
            }
        }

        return $this;
    }

}
