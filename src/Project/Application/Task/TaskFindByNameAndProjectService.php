<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Project;
use App\Project\Domain\TaskDTO;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\Shared\Domain\Exception\TaskNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskFindByNameAndProjectService
{
    private TaskRepositoryInterface $taskRepository;


    public function __construct(
        TaskRepositoryInterface $taskRepository
    )
    {
        $this->taskRepository = $taskRepository;
    }

    public function __invoke(string $name, string $projectName): JsonResponse
    {
        $project = $this->taskRepository->findProjectByName($projectName);
        if (!$project) {
            throw new ProjectNotFoundException();
        }
        if (!$this->taskRepository->checkIfTaskExists($name, $project)) {
            throw new TaskNotFoundException();
        }
        $task = $this->taskRepository->findTaskFromProject($name, $project);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'task' => TaskDTO::fromEntity($task),
        ], 200);
    }

}