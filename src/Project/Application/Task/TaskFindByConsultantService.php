<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\TaskDTO;
use App\Shared\Domain\Exception\ConsultantNotFoundException;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\Shared\Domain\Exception\TaskNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskFindByConsultantService
{
    private TaskRepositoryInterface $taskRepository;


    public function __construct(
        TaskRepositoryInterface $taskRepository,
    )
    {
        $this->taskRepository = $taskRepository;
    }

    public function __invoke(string $email): JsonResponse
    {
        $tasks = $this->taskRepository->findTaskByConsultant($email);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'tasks' => array_map(fn($task) => TaskDTO::fromEntity($task), $tasks),
        ], 200);
    }

}