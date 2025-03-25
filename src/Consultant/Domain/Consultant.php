<?php

namespace App\Consultant\Domain;

use App\Project\Domain\Project;
use App\Project\Domain\Task;
use App\Repository\ConsultantRepository;
use App\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConsultantRepository::class)]
class  Consultant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surnames = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'string', enumType: Profile::class)]
    private Profile $profile;

    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'consultant')]
    private Collection $project;

    #[ORM\ManyToMany(targetEntity: Task::class, mappedBy: 'consultants')]
    private Collection $tasks;

    #[ORM\ManyToMany(targetEntity: Ability::class, mappedBy: 'consultant')]
    private Collection $abilities;


    public function __construct()
    {
        $this->project = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->abilities = new ArrayCollection();
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

    public function getSurnames(): ?string
    {
        return $this->surnames;
    }

    public function setSurnames(string $surnames): static
    {
        $this->surnames = $surnames;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user;
    }

    public function setUserId(User $user_id): static
    {
        $this->user = $user_id;

        return $this;
    }

    public function getProfile(): Profile
    {
        return $this->profile;
    }

    public function setProfile(Profile $profile): void
    {
        $this->profile = $profile;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProject(): Collection
    {
        return $this->project;
    }

    public function addProject(Project $project): static
    {
        if (!$this->project->contains($project)) {
            $this->project->add($project);
            $project->addConsultant($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->project->removeElement($project)) {
            $project->removeConsultant($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */

    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->addConsultant($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            $task->removeConsultant($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Ability>
     */
    public function getAbilities(): Collection
    {
        return $this->abilities;
    }

    public function addAbility(Ability $ability): static
    {
        if (!$this->abilities->contains($ability)) {
            $this->abilities->add($ability);
            $ability->addConsultant($this);
        }

        return $this;
    }

    public function removeAbility(Ability $ability): static
    {
        if ($this->abilities->removeElement($ability)) {
            $ability->removeConsultant($this);
        }

        return $this;
    }
}
