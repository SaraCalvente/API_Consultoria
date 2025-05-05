<?php
declare(strict_types=1);

namespace App\Client\Domain;

class ClientByEmailDTO
{
    public function __construct(
        public string $email
    ) {}
}