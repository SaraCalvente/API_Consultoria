<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\DTO\ActivityHistoryByNameAndProjectDTO;
use App\ActivityHistory\Domain\DTO\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Shared\Domain\Exception\ActivityHistoryNotFoundException;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ActivityHistoryGetByNameAndProjectService
{
    public function __construct(
        private ActivityHistoryRepositoryInterface $activityHistoryRepository,
        private ProjectRepositoryInterface $projectRepository
    ) {}

    public function __invoke(ActivityHistoryByNameAndProjectDTO $dto): ActivityHistoryDTO
    {
        $project = $this->projectRepository->findProjectByName($dto->projectName);
        if (!$project) {
            throw new ProjectNotFoundException();
        }

        if (!$this->activityHistoryRepository->checkIfActivityHistoryFromProjectExists($dto->name, $project)) {
            throw new ActivityHistoryNotFoundException();
        }

        $activity = $this->activityHistoryRepository->findActivityHistoryFromProject($dto->name, $project);
        return ActivityHistoryDTO::fromEntity($activity);
    }

}