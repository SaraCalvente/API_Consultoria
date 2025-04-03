<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskDeleteByNameService
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
        $task = $this->taskRepository->findTaskFromProject($name, $project);
        foreach ($task->getConsultants() as $consultant) {
            $task->removeConsultant($consultant);
        }
        $this->taskRepository->removeTask($task);
        return new JsonResponse(['message' => 'Task deleted successfully'], 200);
    }
}