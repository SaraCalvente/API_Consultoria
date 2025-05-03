<?php

namespace App\ActivityHistory\Domain;

use App\ActivityHistory\Infraestructure\ActivityHistoryRepository;
use App\Project\Domain\Project\Project;
use App\User\Domain\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method expects(\PHPUnit\Framework\MockObject\Rule\InvokedCount $once)
 */
#[ORM\Entity(repositoryClass: ActivityHistoryRepository::class)]
#[ORM\UniqueConstraint(name: "unique_project_name", columns: ["name", "project_id"])]
class ActivityHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'activity_history')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'activity_history')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Project $project = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getName(): string
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public static function createActivityHistory(
        string $name,
        string $description,
        \DateTimeInterface $date,
        User $user,
        Project $project
    ): self {
        $activityHistory = new self();
        $activityHistory->setName($name);
        $activityHistory->setDescription($description);
        $activityHistory->setDate($date);
        $activityHistory->setUser($user);
        $activityHistory->setProject($project);

        return $activityHistory;
    }


}