<?php

declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\DTO\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\User;
use DomainException;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ActivityHistoryGetByConsultantService
{
    public function __construct(
        private ConsultantRepositoryInterface $consultantRepository,
        private ActivityHistoryRepositoryInterface $activityHistoryRepository
    ) {}

    /**
     * @return array{current_consultant_id: int, activities: ActivityHistoryDTO[]}
     */
    public function __invoke(User $user): array
    {
        $consultant = $this->consultantRepository->findConsultantByUser($user);

        if (!$consultant) {
            throw new DomainException('The user ' . $user->getEmail() . ' is not a consultant');
        }

        $activities = $this->activityHistoryRepository->findActivitiesByConsultant($user);

        return [
            'current_consultant_id' => $consultant->getId(),
            'activities' => array_map(fn($activity) => ActivityHistoryDTO::fromEntity($activity), $activities),
        ];
    }
}