<?php

namespace App\Project\Domain\Task;

use App\Consultant\Domain\Consultant\Consultant;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Status;
use App\Project\Infraestructure\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: "unique_project_name", columns: ["name", "project_id"])]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?Status $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $start_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $end_date = null;

    #[ORM\Column(length: 255)]
    private ?string $time_estimation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\ManyToMany(targetEntity: Consultant::class, inversedBy: 'tasks')]
    #[ORM\JoinTable(name: 'task_consultant')]
    private Collection $consultants;

    public function __construct()
    {
        $this->consultants = new ArrayCollection();
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

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTimeEstimation(): ?string
    {
        return $this->time_estimation;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setTimeEstimation(): static
    {
        if (empty($this->start_date) || empty($this->end_date)) {
            return $this;
        }
        $time_estimation = $this->end_date->diff($this->start_date);
        $this->time_estimation = $time_estimation->format('%dd %hh %mm');
        return $this;
    }
    public function getStartDate(): ?\DateTime
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTime $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }
    public function getEndDate(): ?\DateTime
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTime $end_date): static
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Collection<int, Consultant>
     */
    public function getConsultants(): Collection
    {
        return $this->consultants;
    }

    public function addConsultant(Consultant $consultant): self
    {
        if (!$this->consultants->contains($consultant)) {
            $this->consultants[] = $consultant;
        }

        return $this;
    }

    public function removeConsultant(Consultant $consultant): self
    {
        $this->consultants->removeElement($consultant);

        return $this;
    }
}
