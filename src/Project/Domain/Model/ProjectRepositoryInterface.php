<?php

namespace App\Project\Domain\Model;

use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant;
use App\Project\Domain\Project;
use App\User\Domain\User;

interface ProjectRepositoryInterface
{
    public function add(Project $project): void;
    public function findUserByEmail(string $email): User;
    public function findClientByEmail(string $email): ?Client;
    public function findConsultantByEmail(string $email): ?Consultant;
    public function findProjectByName(string $name): ?Project;
    public function checkDates(string $startDate, ?string $endDate): bool;
    public function checkIfProjectExists(string $name): ?Project;
    public function findConsultantById(int $id): ?Consultant;
    public function findClientById(int $id): ?Client;

    public function findProjectByClient(Client $client): array;
    public function findAllProjects(): array;
    public function save(): void;
    public function remove(Project $project): void;


    }