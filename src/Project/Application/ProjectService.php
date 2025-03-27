<?php

namespace App\Project\Application;

use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant;
use App\Consultant\Domain\Profile;
use App\Project\Domain\Project;
use App\Project\Domain\Status;
use App\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\JsonResponse;

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
    public function createProject(string $clientEmail, string $name, string $description, string $startDate, ?string $endDate, string $status, array $consultantsEmails): JsonResponse
    {
        if (!$this->checkDates($startDate, $endDate)) {
            return new JsonResponse(['error' => 'startDate no puede ser nula, ni mayor a endDate, el formato es (Y-m-d)'], 404);
        }

        $project = new Project();
        $clientUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $clientEmail]);
        if (!$clientUser) {
            return new JsonResponse(['error' => 'Client not found'], 404);
        }
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $clientUser->getId()]);
        if (!$client) {
            return new JsonResponse(['error' => 'Client not found'], 404);
        }

        $project->setClient($client);
        $project->setName($name);
        $project->setDescription($description);
        $project->setStartDate(new \DateTime($startDate));
        if ($endDate) {
            $project->setEndDate(new \DateTime($endDate));
        }
        $project->setStatus(Status::from($status));

        foreach ($consultantsEmails as $consultantEmail) {
            $consultantUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $consultantEmail]);
            $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $consultantUser->getId()]);
            if (!$consultant) {
                return new JsonResponse(['error' => 'Consultant not found'], 404);
            }
            $project->addConsultant($consultant);
        }


        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Project created successfully',
            'client_id' => $project->getClient()->getId(),
            'name' => $project->getName(),
            'description' => $project->getDescription(),
            'start_date' => $project->getStartDate()->format('Y-m-d'),
            'end_date' => $project->getEndDate()?->format('Y-m-d'),
            'status' => $project->getStatus(),
            'consultantsId' => array_map(fn($c) => $c->getId(), $project->getConsultant()->toArray())
        ], 201);
    }

    public function getProjectsByUser(int $user_id): JsonResponse
    {
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $user_id]);
        $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $user_id]);

        $projectsAsClient = [];
        if ($client) {
            $projectsAsClient = $this->entityManager->getRepository(Project::class)->findBy(['client' => $client]);
        }

        $projectsAsConsultant = [];
        if($consultant){
            $projectsAsConsultant = $consultant->getProject()->toArray();

        }

        if ($client) {
            $projects = $projectsAsClient;
            $role = 'client';
        } elseif ($consultant) {
            $projects = $projectsAsConsultant;
            $role = 'consultant';
        } else {
            return new JsonResponse(['error' => 'User has no associated projects'], 404);
        }
        return new JsonResponse(array_map(fn($project) => [
            'user_id' => $role === 'client' ? $client->getId() : $consultant->getId(),
            'role' => $role,
            'name' => $project->getName(),
            'description' => $project->getDescription(),
            'start_date' => $project->getStartDate()->format('Y-m-d'),
            'end_date' => $project->getEndDate() ? $project->getEndDate()->format('Y-m-d') : null,
            'status' => $project->getStatus(),
        ], $projects), 200);
    }


        public function getAllProjects(): JsonResponse
    {
        $projects = $this->entityManager->getRepository(Project::class)->findAll();

        $projectsData = [];
        foreach ($projects as $project) {
            $projectsData[] = [
                'client_id' => $project->getClient()->getId(),
                'name' => $project->getName(),
                'description' => $project->getDescription(),
                'start_date' => $project->getStartDate()->format('Y-m-d'),
                'end_date' => $project->getEndDate()?->format('Y-m-d'),
                'status' => $project->getStatus(),
                'consultantsId' => array_map(fn($c) => $c->getId(), $project->getConsultant()->toArray())
            ];
        }

        return new JsonResponse($projectsData, 200);
    }

    public function updateProject(
       string $name, string $description = null, string $status = null, string $endDate = null,
       array $consultantsEmails = null
    ): JsonResponse {
        try {
            $project = $this->entityManager->getRepository(Project::class)->findOneBy(['name' => $name]);
            if (!$project) {
                return new JsonResponse(['error' => 'Client not found'], 404);
            }

            if ($description !== null) {
                $project->setDescription($description);
            }
            if ($status !== null) {
                $project->setStatus(Status::from($status));
            }
            $startDate = $project->getStartDate()->format('Y-m-d');
            try {
                $this->checkDates($startDate, $endDate);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => $e->getMessage()], 400);
            }

            if ($endDate !== null) {
                $project->setEndDate(new \DateTime($endDate));
            }
            if ($consultantsEmails !== null) {
                foreach ($consultantsEmails as $consultantEmail) {
                    $consultantUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $consultantEmail]);
                    $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $consultantUser->getId()]);
                    $project->addConsultant($consultant);
                }
            }

            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Project updated successfully',
                'client_id' => $project->getClient()->getId(),
                'name' => $project->getName(),
                'description' => $project->getDescription(),
                'start_date' => $project->getStartDate()->format('Y-m-d'),
                'end_date' => $project->getEndDate()?->format('Y-m-d'),
                'status' => $project->getStatus(),
                'consultantsId' => array_map(fn($c) => $c->getId(), $project->getConsultant()->toArray())
            ], 200);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteProject(string $name): JsonResponse
    {
        try {
            $project = $this->entityManager->getRepository(Project::class)->findOneBy(['name' => $name]);

            if (!$project) {
                return new JsonResponse(['error' => 'Project not found'], 404);
            }

            $this->entityManager->remove($project);
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Project deleted successfully'], 200);


        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    private function checkDates(string $startDate, string $endDate): bool
    {
        $start = \DateTime::createFromFormat('Y-m-d', $startDate);
        if (!$start) {
            return false;
        }
        if (!empty($endDate) && trim($endDate) !== '') {
            $end = \DateTime::createFromFormat('Y-m-d', $endDate);
            if (!$end) {
                return false;
            }
            if ($start > $end) {
                return false;
            }
        }
        return true;
    }


}