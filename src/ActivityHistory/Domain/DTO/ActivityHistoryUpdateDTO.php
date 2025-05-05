<?php
declare(strict_types=1);

namespace App\ActivityHistory\Domain\DTO;

final readonly class ActivityHistoryUpdateDTO
{
    public function __construct(
        public string $name,
        public string $projectName,
        public ?string $description = null
    ) {}
}