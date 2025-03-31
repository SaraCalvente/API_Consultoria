<?php

namespace App\Client\Infraestructure;

use App\Client\Domain\Client;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Consultant\Domain\Consultant;
use App\Shared\Domain\Exception\ConsultantNotFoundException;
use App\Shared\Domain\Exception\UserNotFoundException;
use App\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use PharIo\Manifest\Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientRepository implements ClientRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    public function add(Client $client): void
    {
        $this->entityManager->persist($client);
        $this->entityManager->flush();
    }

    public function findUserAndClient(array $criteria): array
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy($criteria);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $user]);
        if (!$consultant) {
            throw new ConsultantNotFoundException();
        }

        return [$user, $consultant];
    }

    public function checkIfUserExists(string $email): bool{
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user) {
            return false;
        }
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $user]);
        if ($client) {
            return false;
        }
        return true;
    }
}