<?php
declare(strict_types=1);

namespace App\ActivityHistory\Domain\DTO;

final readonly class ActivityHistoryDeleteDTO
{
    public function __construct(
        public string $projectName,
        public string $name,
    ) {}
}