<?php
declare(strict_types=1);

namespace App\Client\Domain;

class ClientDTO
{
    public static function fromEntity(Client $client): array
    {
        return [
            'client_id' => $client->getId(),
            'user_id' => $client->getUser()->getId(),
            'email' => $client->getUser()->getEmail(),
            'name' => $client->getName(),
            'surnames' => $client->getSurnames(),
            'address' => $client->getAddress(),
            'phone_number' => $client->getPhoneNumber(),
            'roles' => $client->getUser()->getRoles()
        ];
    }
}