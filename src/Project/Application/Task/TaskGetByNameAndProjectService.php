<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Task\TaskDTO;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\Shared\Domain\Exception\TaskNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class TaskGetByNameAndProjectService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private ProjectRepositoryInterface $projectRepository
    )
    {}

    public function __invoke(array $data): JsonResponse
    {
        $project = $this->projectRepository->findProjectByName($data['projectName']);
        if (!$project instanceof \App\Project\Domain\Project\Project) {
            throw new ProjectNotFoundException();
        }
        if (!$this->taskRepository->checkIfTaskFromProjectExists($data['name'], $project)) {
            throw new TaskNotFoundException();
        }
        $task = $this->taskRepository->findTaskFromProject($data['name'], $project);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'task' => TaskDTO::fromEntity($task),
        ], 201);
    }

}