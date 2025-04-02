<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\TaskDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskFindByProjectService
{
    private TaskRepositoryInterface $taskRepository;


    public function __construct(
        TaskRepositoryInterface $taskRepository,
    )
    {
        $this->taskRepository = $taskRepository;
    }

    public function __invoke(string $projectName): JsonResponse
    {
        $tasks = $this->taskRepository->findTasksByProject($projectName);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'tasks' => array_map(fn($task) => TaskDTO::fromEntity($task), $tasks),
        ], 200);
    }
}