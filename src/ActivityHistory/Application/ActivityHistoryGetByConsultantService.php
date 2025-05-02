<?php

declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\ActivityHistory\Infraestructure\ActivityHistoryRepository;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ActivityHistoryGetByConsultantService
{
    public function __construct(
        private ConsultantRepositoryInterface $consultantRepository,
        private ActivityHistoryRepositoryInterface $activityHistoryRepository
    )
    {}

    public function __invoke(User $user): JsonResponse
    {
        $consultant = $this->consultantRepository->findConsultantByUser($user);

        if (!$consultant instanceof \App\Consultant\Domain\Consultant\Consultant) {
            return new JsonResponse(['error' => 'The user ' . $user->getEmail() . ' is not a consultant'], 402);
        }

        $activities = $this->activityHistoryRepository->findActivitiesByConsultant($user);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'current_consultant_id' => $consultant->getId(),
            'activities' => array_map(fn($activity) => ActivityHistoryDTO::fromEntity($activity), $activities),
        ], 200);
    }
}
