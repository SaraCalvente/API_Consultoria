<?php

namespace App\Project\Domain\Model;

use App\Consultant\Domain\Consultant\Consultant;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Task\Task;

interface TaskRepositoryInterface
{
    public function addTask(Task $task): void;

    public function checkIfTaskFromProjectExists(string $taskName, Project $project): bool;

    public function checkDates(string $startDate, ?string $endDate): bool;

    public function findTaskFromProject(string $taskName, Project $project): Task;

    public function findAllTasks(): array;

    public function findTaskByConsultant(Consultant $consultant): array;

    public function findTasksByProject(Project $project): array;

    public function saveTask(): void;
    public function removeTask(Task $task): void;
}