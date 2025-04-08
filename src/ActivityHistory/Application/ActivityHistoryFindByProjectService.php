<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\TaskDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityHistoryFindByProjectService
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

    public function __invoke(string $projectName): JsonResponse
    {
        $project = $this->projectRepository->findProjectByName($projectName);
        $activities = $this->activityHistoryRepository->findActivitiesHistoriesByProject($project);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'tasks' => array_map(fn($activity) => ActivityHistoryDTO::fromEntity($activity), $activities),
        ], 201);
    }
}