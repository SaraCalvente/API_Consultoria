<?php
declare(strict_types=1);

namespace App\ActivityHistory\Domain\DTO;

class ActivityHistoryCreateDTO
{
    public function __construct(
        public string $projectName,
        public string $name,
        public string $description,
        public string $date,
        public string $consultantEmail,
    ) {}
}