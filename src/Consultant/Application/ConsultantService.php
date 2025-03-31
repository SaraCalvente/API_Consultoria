<?php

namespace App\Consultant\Application;

use App\Client\Domain\Client;
use App\Client\Domain\ClientDTO;
use App\Consultant\Domain\Consultant;
use App\Consultant\Domain\ConsultantDTO;
use App\Consultant\Domain\Profile;
use App\Project\Domain\Project;
use App\Shared\Domain\Exception\ClientNotFoundException;
use App\Shared\Domain\Exception\ConsultantNotFoundException;
use App\Shared\Domain\Exception\UserNotFoundException;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ConsultantService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    public function registerConsultant(string $email, string $password, string $name, string $surnames, string $profile): JsonResponse
    {
        if ($this->entityManager->getRepository(User::class)->findOneBy(['email' => $email])) {
            return new JsonResponse(['error' => 'Email is already registered'], 409);
        }

        $user = new User();
        $user->setEmail(new EmailValueObject($email));

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_CONSULTANT']);
        $this->entityManager->persist($user);

        $consultant = new Consultant();
        $consultant->setName($name);
        $consultant->setSurnames($surnames);
        $consultant->setProfile(Profile::from($profile));
        $consultant->setUser($user);
        $this->entityManager->persist($consultant);

        $this->entityManager->flush();

        return new JsonResponse(ConsultantDTO::fromEntity($consultant), 201);
    }



    public function getConsultant(int $userId): JsonResponse
    {
        $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $userId]);

        if (!$consultant) {
            throw new ConsultantNotFoundException();
        }

        return new JsonResponse(ConsultantDTO::fromEntity($consultant));
    }

    public function getAllConsultants(): JsonResponse
    {
        $consultants = $this->entityManager->getRepository(Consultant::class)->findAll();

        $consultantData = [];
        foreach ($consultants as $consultant) {
            $consultantData[] = ConsultantDTO::fromEntity($consultant);
        }

        return new JsonResponse($consultantData, 200);
    }

    public function updateConsultantById(
        int $userId, ?string $profile = null
    ): JsonResponse {
        return $this->modifyConsultant(['id' => $userId], $profile);
    }

    public function updateConsultantByEmail(
        string $email, ?string $profile = null
    ): JsonResponse {
        return $this->modifyConsultant(['email' => $email], $profile);
    }

    private function modifyConsultant(array $criteria, ?string $profile): JsonResponse
    {
        [, $consultant] = $this->findUserAndConsultant($criteria);


        if ($profile !== null) {
            $consultant->setProfile(Profile::from($profile));
        }
        $this->entityManager->flush();

        return new JsonResponse(ConsultantDTO::fromEntity($consultant));
    }

    public function deleteConsultantById(int $userId): JsonResponse
    {
        return $this->removeConsultant(['id' => $userId]);
    }

    public function deleteConsultantByEmail(string $email): JsonResponse
    {
        return $this->removeConsultant(['email' => $email]);
    }

    private function removeConsultant(array $criteria): JsonResponse
    {
        [$user, $consultant] = $this->findUserAndConsultant($criteria);

        $projects = $consultant->getProject();
        if (count($projects) > 0) {
            return new JsonResponse(['error' => 'Cannot delete client because there are associated projects.'], 400);
        }

        $this->entityManager->remove($consultant);
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Consultant and associated user deleted successfully'], 200);
    }

    private function findUserAndConsultant(array $criteria): array
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

}
