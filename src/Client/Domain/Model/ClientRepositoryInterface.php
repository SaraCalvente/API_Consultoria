<?php
declare(strict_types=1);

namespace App\Client\Domain\Model;

use App\Client\Domain\Client;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

interface ClientRepositoryInterface
{
    public function findClientByUser(User $user): ?Client;
    public function checkIfClientExists(User $user): bool;
    public function findAllClients(): array;
    public function updateClient(User $user, ?string $address, ?string $phoneNumber): Client;
    public function deleteClient(Client $client): JsonResponse;
    public function addClient(Client $client): void;
    public function saveClient(): void;
    public function removeClient(Client $client): void;




}