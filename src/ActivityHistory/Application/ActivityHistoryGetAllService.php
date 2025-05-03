<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\DTO\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Shared\Domain\Exception\ActivityHistoryNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ActivityHistoryGetAllService
{
    public function __construct(
        private ActivityHistoryRepositoryInterface $activityHistoryRepository
    ) {}

    /**
     * @return ActivityHistoryDTO[]
     */
    public function __invoke(): array
    {
        $activities = $this->activityHistoryRepository->findAllActivityHistories();

        if ($activities === []) {
            throw new ActivityHistoryNotFoundException();
        }

        return array_map(fn($activity) => ActivityHistoryDTO::fromEntity($activity), $activities);
    }

}