<?php

namespace App\User\Domain;

use App\ActivityHistory\Domain\ActivityHistory;
use App\User\Domain\ValueObject\EmailValueObject;
use App\User\Infraestructure\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "email_vo",length: 180, unique: true)]
    private ?EmailValueObject $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToMany(targetEntity: Notification::class, mappedBy: 'user')]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ActivityHistory::class)]
    private Collection $activityHistories;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
        $this->activityHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?EmailValueObject
    {
        return $this->email;
    }

    public function setEmail(EmailValueObject $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->addUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            $notification->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, ActivityHistory>
     */
    public function getActivityHistories(): Collection
    {
        return $this->activityHistories;
    }

    public function addActivityHistory(ActivityHistory $activityHistory): static
    {
        if (!$this->activityHistories->contains($activityHistory)) {
            $this->activityHistories->add($activityHistory);
            $activityHistory->setUser($this);
        }

        return $this;
    }

    public function removeActivityHistory(ActivityHistory $activityHistory): static
    {
        // set the owning side to null (unless already changed)
        if ($this->activityHistories->removeElement($activityHistory) && $activityHistory->getUser() === $this) {
            $activityHistory->setUser(null);
        }

        return $this;
    }
}
