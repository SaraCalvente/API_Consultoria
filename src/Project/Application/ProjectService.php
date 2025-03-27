<?php

namespace App\Project\Application;

use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant;
use App\Consultant\Domain\Profile;
use App\Project\Domain\Project;
use App\Project\Domain\Status;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProjectService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function createProject(string $clientEmail, string $name, string $description, string $startDate, string $endDate, string $status, array $consultantsIds): JsonResponse
    {
        $project = new Project();
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['id' => $clientEmail]);

        if (!$client) {
            return new JsonResponse(['error' => 'Client not found'], 404);
        }

        $project->setClient($client);
        $project->setName($name);
        $project->setDescription($description);
        $startDate = new \DateTime($startDate);
        $project->setStartDate($startDate);
        $endDate = new \DateTime($endDate);
        $project->setEndDate($endDate);
        $project->setStatus(Status::from($status));

        foreach ($consultantsIds as $consultant) {
            $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['id' => $consultant]);
            if (!$consultant) {
                return new JsonResponse(['error' => 'Consultant not found'], 404);
            }
            $project->addConsultant($consultant);
        }


        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'User registered successfully',
            'client_id' => $project->getClient()->getId(),
            'name' => $project->getName(),
            'description' => $project->getDescription(),
            'start_date' => $project->getStartDate()->format('Y-m-d'),
            'end_date' => $project->getEndDate()->format('Y-m-d'),
            'status' => $project->getStatus(),
            'consultants' => array_map(fn($c) => $c->getId(), $project->getConsultant()->toArray())
        ], 201);
    }



    public function getConsultant(int $userId): array
    {
        $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $userId]);

        if (!$consultant) {
            throw new \Exception('Consultant not found');
        }

        return [
            'consultant_id' => $consultant->getId(),
            'name' => $consultant->getName(),
            'email' => $consultant->getUser()->getEmail(),
            'surnames' => $consultant->getSurnames(),
            'profile' => $consultant->getProfile(),
        ];
    }

    public function getAllConsultants(): JsonResponse
    {
        $consultants = $this->entityManager->getRepository(Consultant::class)->findAll();

        $consultantData = [];
        foreach ($consultants as $consultant) {
            $consultantData[] = [
                'consultant_id' => $consultant->getId(),
                'user_id' => $consultant->getUser()->getId(),
                'email' => $consultant->getUser()->getEmail(),
                'name' => $consultant->getName(),
                'surnames' => $consultant->getSurnames(),
                'profile' => $consultant->getProfile(),
            ];
        }

        return new JsonResponse($consultantData, 200);
    }

    public function updateConsultant(
        int $userId, string $profile = null
    ): JsonResponse {
        try {
            $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $userId]);
            if (!$consultant) {
                return new JsonResponse(['error' => 'Client not found'], 404);
            }
            $user = $consultant->getUser();

            if ($profile !== null) {
                $consultant->setProfile(Profile::from($profile));
            }

            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Client updated successfully',
                'user_id' => $user->getId(),
                'client_id' => $consultant->getId(),
                'email' => $user->getEmail(),
                'name' => $consultant->getName(),
                'surnames' => $consultant->getSurnames(),
                'phone_number' => $consultant->getProfile(),
            ], 200);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteConsultant(int $userId): JsonResponse
    {
        try {
            $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $userId]);

            if (!$consultant) {
                return new JsonResponse(['error' => 'Consultant not found'], 404);
            }

            $projects = $consultant->getProject();

            if (!$projects) {
                throw new \Exception('Cannot delete consultant because there are associated projects.');
            }

            $this->entityManager->remove($consultant);
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Consultant and associated user deleted successfully'], 200);


        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}