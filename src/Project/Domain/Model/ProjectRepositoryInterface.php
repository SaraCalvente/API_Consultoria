<?php

namespace App\Project\Domain\Model;

use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant;
use App\Project\Domain\Project;
use App\User\Domain\User;

interface ProjectRepositoryInterface
{
    public function addProject(Project $project): void;
    public function findProjectByName(string $name): ?Project;
    public function checkDates(string $startDate, ?string $endDate): bool;
    public function checkIfProjectExists(string $name): bool;
    public function findProjectByClient(Client $client): array;
    public function findAllProjects(): array;
    public function saveProject(): void;
    public function removeProject(Project $project): void;


    }