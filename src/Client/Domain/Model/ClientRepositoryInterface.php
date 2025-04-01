<?php

namespace App\Client\Domain\Model;

use App\Client\Domain\Client;
use Symfony\Component\HttpFoundation\JsonResponse;

interface ClientRepositoryInterface
{
    public function findUserAndClient(array $criteria): array;
    public function checkIfUserExists(string $email): bool;
    public function findClientById(int $user): ?Client;
    public function findAllClients(): array;
    public function modifyClient(array $criteria, ?string $address, ?string $phoneNumber): JsonResponse;
    public function removeClient(array $criteria): JsonResponse;

}