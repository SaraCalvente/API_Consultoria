<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\DTO\ActivityHistoryDeleteDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use DomainException;

final readonly class ActivityHistoryDeleteByNameService
{
    public function __construct(
        private ActivityHistoryRepositoryInterface $activityHistoryRepository,
        private ProjectRepositoryInterface $projectRepository
    ) {}

    public function __invoke(ActivityHistoryDeleteDTO $dto): void
    {
        if (!$this->projectRepository->checkIfProjectExists($dto->projectName)) {
            throw new DomainException('Project with name ' . $dto->projectName . ' was not found');
        }

        $project = $this->projectRepository->findProjectByName($dto->projectName);

        if (!$this->activityHistoryRepository->checkIfActivityHistoryFromProjectExists($dto->name, $project)) {
            throw new DomainException('An activity with this name (' . $dto->name . ') in project ' . $dto->projectName . ' was not found');
        }

        $activity = $this->activityHistoryRepository->findActivityHistoryFromProject($dto->name, $project);
        $this->activityHistoryRepository->removeActivityHistory($activity);
    }
}