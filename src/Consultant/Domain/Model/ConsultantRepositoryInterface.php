<?php

namespace App\Consultant\Domain\Model;

use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

interface ConsultantRepositoryInterface
{
    public function findUserAndConsultant(array $criteria): array;
    public function checkIfUserExists(string $email): bool;
    public function findConsultantById(int $user): ?Consultant;
    public function findAllConsultants(): array;
    public function modifyConsultant(array $criteria, ?string $profile): JsonResponse;
    public function removeConsultant(array $criteria): JsonResponse;
    public function save(): void;
    public function findUserByEmail(string $email): User;
    public function findConsultantByEmail(string $email): ?Consultant;

}