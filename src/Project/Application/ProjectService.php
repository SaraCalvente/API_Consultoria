<?php

namespace App\Project\Application;

use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant;
use App\Project\Domain\Project;
use App\Project\Domain\ProjectDTO;
use App\Project\Domain\Status;
use App\Shared\Domain\Exception\ClientNotFoundException;
use App\Shared\Domain\Exception\ConsultantNotFoundException;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\Shared\Domain\Exception\UserNotFoundException;
use App\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
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
    public function createProject(
        string $clientEmail, string $name,
        string $description, string $startDate,
        ?string $endDate, string $status,
        array $consultantsEmails
    ): JsonResponse
    {
        if ($this->entityManager->getRepository(Project::class)->findOneBy(['name' => $name])) {
            return new JsonResponse(['error' => 'A project with this name is already registered, duplicated KEY'], 409);
        }

        if (!$this->checkDates($startDate, $endDate)) {
            return new JsonResponse(['error' => 'Invalid date range or format (Y-m-d)'], 404);
        }

        $client = $this->findClientByEmail($clientEmail);

        $project = new Project();
        $project
            ->setClient($client)
            ->setName($name)
            ->setDescription($description)
            ->setStartDate(new \DateTime($startDate));
        if ($endDate) {
            $project->setEndDate(new \DateTime($endDate));
        }
        $project->setStatus(Status::from($status));

        foreach ($consultantsEmails as $consultantEmail) {
            $consultant = $this->findConsultantByEmail($consultantEmail);
            $project->addConsultant($consultant);
        }

        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Project created successfully',
            'project' => ProjectDTO::fromEntity($project),
        ], 201);
    }

    public function getProjectsByUser(int $user_id): JsonResponse
    {
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $user_id]);
        $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $user_id]);

        if (!$client && !$consultant) {
            return new JsonResponse(['error' => 'User has no associated projects'], 404);
        }

        $projects = $client ? $this->entityManager->getRepository(Project::class)->findBy(['client' => $client]) : $consultant->getProject()->toArray();
        return new JsonResponse([
            'message' => 'Projects retrieved successfully',
            $client ? 'current_client_id' : 'current_consultant_id' => $client ? $client->getId() : $consultant->getId(),
            'projects' => array_map(fn($project) => ProjectDTO::fromEntity($project), $projects),
        ], 200);
    }


    public function getAllProjects(): JsonResponse
    {
        $projects = $this->entityManager->getRepository(Project::class)->findAll();

        $projectsData = [];
        foreach ($projects as $project) {
            $projectsData[] = ProjectDTO::fromEntity($project);
        }

        return new JsonResponse($projectsData, 200);
    }

    public function updateProject(
       string $name, string $description = null, string $status = null, string $endDate = null,
       array $consultantsEmails = null
    ): JsonResponse {
        $project = $this->findProjectByName($name);

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
            $this->checkDates($startDate, $endDate);
            $project->setEndDate(new \DateTime($endDate));
        }
        if ($consultantsEmails !== null) {
            foreach ($consultantsEmails as $consultantEmail) {
                $consultant = $this->findConsultantByEmail($consultantEmail);
                if (!$project->getConsultant()->contains($consultant)) {
                    $project->addConsultant($consultant);
                }
            }
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Project updated successfully',
            'project' => ProjectDTO::fromEntity($project),
        ], 200);

    }

    public function deleteProject(string $name): JsonResponse
    {
        $project = $this->findProjectByName($name);
        foreach ($project->getConsultant() as $consultant) {
            $project->removeConsultant($consultant);
        }
        $this->entityManager->remove($project);
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'Project deleted successfully'], 200);
    }

    private function findUserByEmail(string $email): User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    private function findClientByEmail(string $email): Client
    {
        $user = $this->findUserByEmail($email);
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $user]);
        if (!$client) {
            throw new ClientNotFoundException();
        }
        return $client;
    }

    private function findConsultantByEmail(string $email): Consultant
    {
        $user = $this->findUserByEmail($email);
        $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $user]);
        if (!$consultant) {
            throw new ConsultantNotFoundException();
        }
        return $consultant;
    }

    private function findProjectByName(string $name): Project
    {
        $project = $this->entityManager->getRepository(Project::class)->findOneBy(['name' => $name]);
        if (!$project) {
            throw new ProjectNotFoundException();
        }
        return $project;
    }

    private function checkDates(string $startDate, ?string $endDate): bool
    {
        $start = \DateTime::createFromFormat('Y-m-d', $startDate);
        if (!$start) return false;
        if ($endDate) {
            $end = \DateTime::createFromFormat('Y-m-d', $endDate);
            return $end && $start <= $end;
        }
        return true;
    }

}