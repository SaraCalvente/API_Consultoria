<?php

namespace App\Consultant\Domain\Ability;

use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Infraestructure\AbilityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbilityRepository::class)]
#[ORM\UniqueConstraint(name: "unique_ability_name", columns: ["name", "level"])]
class Ability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', enumType: Level::class)]
    private ?Level $level = null;

    #[ORM\ManyToMany(targetEntity: Consultant::class, inversedBy: 'abilities')]
    private Collection $consultant;

    public function __construct()
    {
        $this->consultant = new ArrayCollection();
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

    public function getLevel(): Level
    {
        return $this->level;
    }

    public function setLevel(Level $level): void
    {
        $this->level = $level;
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
}
