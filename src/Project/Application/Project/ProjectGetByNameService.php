<?php

namespace App\Project\Application\Project;

use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\ProjectDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectGetByNameService
{
    private ProjectRepositoryInterface $projectRepository;


    public function __construct(
        ProjectRepositoryInterface $projectRepository
    )
    {
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(array $data): JsonResponse
    {
        $project = $this->projectRepository->findProjectByName($data['name']);
        return new JsonResponse([
            'message' => 'Project retrieved successfully',
            'project' => ProjectDTO::fromEntity($project),
        ], 200);
    }
}