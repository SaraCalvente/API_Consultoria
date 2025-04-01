<?php

namespace App\Project\Application\Project;

use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\ProjectDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectFindByUserService
{
    private ProjectRepositoryInterface $projectRepository;


    public function __construct(
        ProjectRepositoryInterface $projectRepository
    )
    {
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(int $user_id): JsonResponse
    {
        $client = $this->projectRepository->findClientById($user_id);
        $consultant = $this->projectRepository->findConsultantById($user_id);

        if (!$client && !$consultant) {
            return new JsonResponse(['error' => 'User has no associated projects'], 404);
        }

        $projects = $client ? $this->projectRepository->findProjectByClient($client): $consultant->getProject()->toArray();
        return new JsonResponse([
            'message' => 'Projects retrieved successfully',
            $client ? 'current_client_id' : 'current_consultant_id' => $client ? $client->getId() : $consultant->getId(),
            'projects' => array_map(fn($project) => ProjectDTO::fromEntity($project), $projects),
        ], 200);
    }

}