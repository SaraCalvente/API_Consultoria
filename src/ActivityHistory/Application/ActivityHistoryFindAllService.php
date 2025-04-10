<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityHistoryFindAllService
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;


    public function __construct(
        ActivityHistoryRepositoryInterface $activityHistoryRepository
    )
    {
        $this->activityHistoryRepository = $activityHistoryRepository;
    }

    public function __invoke(): JsonResponse
    {
        $activities = $this->activityHistoryRepository->findAllActivityHistories();

        if (!$activities) {
            return new JsonResponse(['error' => 'There are no activities'], 404);
        }

        return new JsonResponse([
            'message' => 'Activities retrieved successfully',
            'tasks' => array_map(fn($activity) => ActivityHistoryDTO::fromEntity($activity), $activities),
        ], 200);
    }
}