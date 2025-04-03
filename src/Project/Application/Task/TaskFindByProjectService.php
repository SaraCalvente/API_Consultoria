<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\TaskDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskFindByProjectService
{
    private TaskRepositoryInterface $taskRepository;
    private ProjectRepositoryInterface $projectRepository;


    public function __construct(
        TaskRepositoryInterface $taskRepository,
        ProjectRepositoryInterface $projectRepository
    )
    {
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(string $projectName): JsonResponse
    {
        $project = $this->projectRepository->findProjectByName($projectName);
        $tasks = $this->taskRepository->findTasksByProject($project);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'tasks' => array_map(fn($task) => TaskDTO::fromEntity($task), $tasks),
        ], 200);
    }
}