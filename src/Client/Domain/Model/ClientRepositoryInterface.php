<?php

namespace App\Client\Domain\Model;

use App\Client\Domain\Client;

interface ClientRepositoryInterface
{
    public function findUserAndClient(array $criteria): array;
    public function checkIfUserExists(string $email): bool;

}