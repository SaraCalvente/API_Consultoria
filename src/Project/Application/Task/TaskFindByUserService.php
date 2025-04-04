<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\ProjectDTO;
use App\Project\Domain\TaskDTO;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskFindByUserService
{
    private ConsultantRepositoryInterface $consultantRepository;

    public function __construct(
        ConsultantRepositoryInterface $consultantRepository,
    )
    {
        $this->consultantRepository = $consultantRepository;
    }

    public function __invoke(User $user): JsonResponse
    {
        $consultant = $this->consultantRepository->findConsultantByUser($user);

        if (!$consultant) {
            return new JsonResponse(['error' => 'User has no associated tasks'], 404);
        }

        $tasks = $consultant->getTasks()->toArray();
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'current_consultant_id' => $consultant->getId(),
            'tasks' => array_map(fn($task) => TaskDTO::fromEntity($task), $tasks),
        ], 200);
    }

}