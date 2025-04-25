<?php

namespace App\Project\Application\Project;

use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\ProjectDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectGetAllService
{
    private ProjectRepositoryInterface $projectRepository;


    public function __construct(
        ProjectRepositoryInterface $projectRepository
    )
    {
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(): JsonResponse
    {
        $projects = $this->projectRepository->findAllProjects();

        $projectsData = [];
        foreach ($projects as $project) {
            $projectsData[] = ProjectDTO::fromEntity($project);
        }

        return new JsonResponse($projectsData, 200);
    }
}