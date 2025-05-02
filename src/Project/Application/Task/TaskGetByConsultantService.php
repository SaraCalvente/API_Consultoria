<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Task\TaskDTO;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskGetByConsultantService
{
    public function __construct(private ConsultantRepositoryInterface $consultantRepository, private TaskRepositoryInterface $taskRepository)
    {
    }

    public function __invoke(User $user): JsonResponse
    {
        $consultant = $this->consultantRepository->findConsultantByUser($user);
        $tasks = $this->taskRepository->findTaskByConsultant($consultant);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'current_consultant_id' => $consultant->getId(),
            'tasks' => array_map(fn($task) => TaskDTO::fromEntity($task), $tasks),
        ], 200);
    }

}