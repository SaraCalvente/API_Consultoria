<?php
declare(strict_types=1);

namespace App\ActivityHistory\Domain\DTO;

use App\ActivityHistory\Domain\ActivityHistory;

class ActivityHistoryDTO
{

    public function __construct(
        public string $name,
        public string $description,
        public int $projectId,
        public string $date,
        public int $userId,
    )
    {}

    public static function fromEntity(ActivityHistory $activityHistory): self
    {
        return new self(
            $activityHistory->getName(),
            $activityHistory->getDescription(),
            $activityHistory->getProject()->getId(),
            $activityHistory->getDate()->format('Y-m-d'),
            $activityHistory->getUser()->getId(),
        );
    }

}