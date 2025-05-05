<?php
declare(strict_types=1);

namespace App\Client\Domain;

use App\ActivityHistory\Domain\ActivityHistory;
use App\User\Domain\ValueObject\EmailValueObject;

class ClientDTO
{
    public function __construct(
        public int $clientId,
        public int $userId,
        public EmailValueObject $email,
        public string $name,
        public string $surnames,
        public string $address,
        public string $phoneNumber,
        public array $roles
    ) {}

    public static function fromEntity(Client $client): self
    {
        return new self(
            $client->getId(),
            $client->getUser()->getId(),
            $client->getUser()->getEmail(),
            $client->getName(),
            $client->getSurnames(),
            $client->getAddress(),
            $client->getPhoneNumber(),
            $client->getUser()->getRoles()
        );
    }

}