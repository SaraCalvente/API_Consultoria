<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\DTO\ActivityHistoryByProjectDTO;
use App\ActivityHistory\Domain\DTO\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ActivityHistoryGetByProjectService
{
    public function __construct(
        private ActivityHistoryRepositoryInterface $activityHistoryRepository,
        private ProjectRepositoryInterface $projectRepository
    ) {}

    /**
     * @return ActivityHistoryDTO[]
     */
    public function __invoke(ActivityHistoryByProjectDTO $dto): array
    {
        if (!$this->projectRepository->checkIfProjectExists($dto->projectName)) {
            throw new ProjectNotFoundException();
        }

        $project = $this->projectRepository->findProjectByName($dto->projectName);
        $activities = $this->activityHistoryRepository->findActivitiesHistoriesByProject($project);

        return array_map(
            fn($activity) => ActivityHistoryDTO::fromEntity($activity),
            $activities
        );
    }
}