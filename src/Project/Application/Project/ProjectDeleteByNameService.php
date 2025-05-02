<?php

namespace App\Project\Application\Project;

use App\Project\Domain\Model\ProjectRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectDeleteByNameService
{
    public function __construct(private ProjectRepositoryInterface $projectRepository)
    {
    }

    public function __invoke(array $data): JsonResponse
    {
        if (!$this->projectRepository->checkIfProjectExists($data['name'])) {
            return new JsonResponse(['error' => 'The project (' . $data['name'] . ') does not exist'], 400);
        }
        $project = $this->projectRepository->findProjectByName($data['name']);
        foreach ($project->getConsultant() as $consultant) {
            $project->removeConsultant($consultant);
        }
        $this->projectRepository->removeProject($project);
        return new JsonResponse(['message' => 'Project deleted successfully'], 201);
    }
}