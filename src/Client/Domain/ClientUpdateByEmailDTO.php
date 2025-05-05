<?php
declare(strict_types=1);

namespace App\Client\Domain;

class ClientUpdateByEmailDTO
{
    public function __construct(
        public string $email,
        public ?string $address = null,
        public ?string $phoneNumber = null
    ) {}
}