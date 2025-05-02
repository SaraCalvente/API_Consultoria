<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Task\TaskDTO;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskGetByConsultantEmailService
{
    public function __construct(private TaskRepositoryInterface $taskRepository, private ConsultantRepositoryInterface $consultantRepository, private UserRepositoryInterface $userRepository)
    {
    }

    public function __invoke(array $data): JsonResponse
    {
        $user = $this->userRepository->findUserByEmailOrFail($data['email']);
        $consultant = $this->consultantRepository->findConsultantByUser($user);
        $tasks = $this->taskRepository->findTaskByConsultant($consultant);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'tasks' => array_map(fn($task) => TaskDTO::fromEntity($task), $tasks),
        ], 200);
    }

}