<?php

namespace App\Project\Application\Project;

use App\Project\Domain\Model\ProjectRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectDeleteByNameService
{
    private ProjectRepositoryInterface $projectRepository;


    public function __construct(
        ProjectRepositoryInterface $projectRepository
    )
    {
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(string $name): JsonResponse
    {
        if (!$this->projectRepository->checkIfProjectExists($name)) {
            return new JsonResponse(['error' => 'The project (' . $name . ') does not exist'], 400);
        }
        $project = $this->projectRepository->findProjectByName($name);
        foreach ($project->getConsultant() as $consultant) {
            $project->removeConsultant($consultant);
        }
        $this->projectRepository->removeProject($project);
        return new JsonResponse(['message' => 'Project deleted successfully'], 201);
    }
}