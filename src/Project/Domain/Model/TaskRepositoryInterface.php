<?php

namespace App\Project\Domain\Model;

use App\Project\Domain\Project;
use App\Project\Domain\Task;

interface TaskRepositoryInterface
{
    public function add(Task $task): void;
    public function checkIfProjectExists(string $name): ?Project;
    public function checkIfTaskExists(string $name, Project $project): ?Task;
    public function checkDates(string $startDate, ?string $endDate): bool;
    public function findProjectByName(string $name): ?Project;
}