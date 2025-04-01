<?php

namespace App\Client\Application;

use App\Client\Domain\Model\ClientRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientUpdateByEmail
{
    private ClientRepositoryInterface $clientRepository;


    public function __construct(
        ClientRepositoryInterface $clientRepository
    )
    {
        $this->clientRepository = $clientRepository;
    }

    public function __invoke(
        string $email, ?string $address = null, ?string $phoneNumber = null
    ): JsonResponse {
        return $this->clientRepository->modifyClient(['email' => $email], $address, $phoneNumber);
    }
}