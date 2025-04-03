<?php

namespace App\Project\Application\Project;

use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project;
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
        $project = $this->projectRepository->findProjectByName($name);
        if (!$this->projectRepository->checkIfProjectExists($project)) {
            return new JsonResponse(['error' => 'The project ' . $project->getName() . ' does not exist'], 400);
        }
        foreach ($project->getConsultant() as $consultant) {
            $project->removeConsultant($consultant);
        }
        $this->projectRepository->removeProject($project);
        return new JsonResponse(['message' => 'Project deleted successfully'], 200);
    }
}