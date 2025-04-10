<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\ActivityHistoryDTO;
use App\ActivityHistory\Infraestructure\ActivityHistoryRepository;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityHistoryFindByConsultantService
{

    private ConsultantRepositoryInterface $consultantRepository;
    private ActivityHistoryRepository $activityHistoryRepository;

    public function __construct(
        ConsultantRepositoryInterface $consultantRepository,
        ActivityHistoryRepository $activityHistoryRepository
    )
    {
        $this->consultantRepository = $consultantRepository;
        $this->activityHistoryRepository = $activityHistoryRepository;
    }

    public function __invoke(User $user): JsonResponse
    {
        if (!$this->consultantRepository->checkIfConsultantExists($user)) {
            return new JsonResponse(['error' => 'The user ' . $user->getEmail() . ' is not a consultant'], 400);
        }
        $consultant = $this->consultantRepository->findConsultantByUser($user);

        if (!$consultant) {
            return new JsonResponse(['error' => 'Consultant has no associated activities'], 402);
        }

        $activities = $this->activityHistoryRepository->findActivitiesByConsultant($user);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'current_consultant_id' => $consultant->getId(),
            'activities' => array_map(fn($activity) => ActivityHistoryDTO::fromEntity($activity), $activities),
        ], 200);
    }

}