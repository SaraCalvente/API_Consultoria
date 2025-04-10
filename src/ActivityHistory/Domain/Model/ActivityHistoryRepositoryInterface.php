<?php

namespace App\ActivityHistory\Domain\Model;

use App\ActivityHistory\Domain\ActivityHistory;
use App\Project\Domain\Project\Project;
use App\User\Domain\User;

interface ActivityHistoryRepositoryInterface
{
    public function addActivityHistory(ActivityHistory $activityHistory): void;
    public function checkIfActivityHistoryFromProjectExists(string $activityHistoryName, Project $project): bool;

    public function findActivityHistoryFromProject(string $activityHistoryName, Project $project): ActivityHistory;

    public function findAllActivityHistories(): array;
    public function findActivitiesHistoriesByProject(Project $project): array;

    public function saveActivityHistory(): void;
    public function removeActivityHistory(ActivityHistory $activityHistory): void;
    public function findActivitiesByConsultant(User $user): array;

}