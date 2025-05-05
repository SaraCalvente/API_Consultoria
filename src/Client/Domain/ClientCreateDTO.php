<?php
declare(strict_types=1);

namespace App\Client\Domain;

class ClientCreateDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public string $name,
        public string $surnames,
        public string $address,
        public string $phoneNumber
    ) {}
}