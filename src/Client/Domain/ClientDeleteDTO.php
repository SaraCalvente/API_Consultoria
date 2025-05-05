<?php
declare(strict_types=1);

namespace App\Client\Domain;

final readonly class ClientDeleteDTO
{
    public function __construct(public string $email) {}
}