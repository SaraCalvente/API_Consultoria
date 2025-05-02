<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Task\TaskDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskGetByProjectService
{
    public function __construct(private TaskRepositoryInterface $taskRepository, private ProjectRepositoryInterface $projectRepository)
    {
    }

    public function __invoke(array $data): JsonResponse
    {
        $project = $this->projectRepository->findProjectByName($data['projectName']);
        $tasks = $this->taskRepository->findTasksByProject($project);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'tasks' => array_map(fn($task) => TaskDTO::fromEntity($task), $tasks),
        ], 201);
    }
}