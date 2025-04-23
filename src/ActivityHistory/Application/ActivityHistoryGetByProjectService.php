<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityHistoryGetByProjectService
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ProjectRepositoryInterface $projectRepository;


    public function __construct(
        ActivityHistoryRepositoryInterface $activityHistoryRepository,
        ProjectRepositoryInterface $projectRepository
    )
    {
        $this->activityHistoryRepository = $activityHistoryRepository;
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(array $data): JsonResponse
    {
        $project = $this->projectRepository->findProjectByName($data['projectName']);
        $activities = $this->activityHistoryRepository->findActivitiesHistoriesByProject($project);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'tasks' => array_map(fn($activity) => ActivityHistoryDTO::fromEntity($activity), $activities),
        ], 201);
    }
}