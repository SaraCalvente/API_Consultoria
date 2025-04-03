<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\TaskDTO;
use App\Shared\Domain\Exception\ConsultantNotFoundException;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\Shared\Domain\Exception\TaskNotFoundException;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskFindByConsultantService
{
    private TaskRepositoryInterface $taskRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;


    public function __construct(
        TaskRepositoryInterface $taskRepository,
        ConsultantRepositoryInterface $consultantRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->taskRepository = $taskRepository;
        $this->consultantRepository = $consultantRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(string $email): JsonResponse
    {
        $user = $this->userRepository->findUserByEmail($email);
        $consultant = $this->consultantRepository->findConsultantByUser($user);
        $tasks = $this->taskRepository->findTaskByConsultant($consultant);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'tasks' => array_map(fn($task) => TaskDTO::fromEntity($task), $tasks),
        ], 200);
    }

}