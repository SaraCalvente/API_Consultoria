<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskDeleteByNameService
{
    public function __construct(private TaskRepositoryInterface $taskRepository, private ProjectRepositoryInterface $projectRepository)
    {
    }

    public function __invoke(array $data): JsonResponse
    {
        $project = $this->projectRepository->findProjectByName($data['projectName']);
        $task = $this->taskRepository->findTaskFromProject($data['name'], $project);
        foreach ($task->getConsultants() as $consultant) {
            $task->removeConsultant($consultant);
        }
        $this->taskRepository->removeTask($task);
        return new JsonResponse(['message' => 'Task deleted successfully'], 200);
    }
}