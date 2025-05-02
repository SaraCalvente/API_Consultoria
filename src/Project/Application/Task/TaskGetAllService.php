<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Task\TaskDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskGetAllService
{
    public function __construct(private TaskRepositoryInterface $taskRepository)
    {
    }

    public function __invoke(): JsonResponse
    {
        $tasks = $this->taskRepository->findAllTasks();

        if ($tasks === []) {
            return new JsonResponse(['error' => 'There are no tasks'], 404);
        }

        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'tasks' => array_map(fn($task) => TaskDTO::fromEntity($task), $tasks),
        ], 200);
    }
}