<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Task\TaskDTO;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\Shared\Domain\Exception\TaskNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskFindByNameAndProjectService
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

    public function __invoke(string $name, string $projectName): JsonResponse
    {
        $project = $this->projectRepository->findProjectByName($projectName);
        if (!$project) {
            throw new ProjectNotFoundException();
        }
        if (!$this->taskRepository->checkIfTaskFromProjectExists($name, $project)) {
            throw new TaskNotFoundException();
        }
        $task = $this->taskRepository->findTaskFromProject($name, $project);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'task' => TaskDTO::fromEntity($task),
        ], 201);
    }

}