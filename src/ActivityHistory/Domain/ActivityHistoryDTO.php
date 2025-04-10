<?php
declare(strict_types=1);

namespace App\ActivityHistory\Domain;

class ActivityHistoryDTO
{
    public static function fromEntity(ActivityHistory $activityHistory): array
    {
        return [
            'activity_history_id' => $activityHistory->getId(),
            'name' => $activityHistory->getName(),
            'description' => $activityHistory->getDescription(),
            'project_id' => $activityHistory->getProject()->getId(),
            'date' => $activityHistory->getDate()->format('Y-m-d'),
            'user_id' => $activityHistory->getUser()->getId(),
        ];
    }
}