<?php

namespace App\Client\Application;

use App\Client\Domain\Model\ClientRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientUpdateByIdService
{
    private ClientRepositoryInterface $clientRepository;


    public function __construct(
        ClientRepositoryInterface $clientRepository
    )
    {
        $this->clientRepository = $clientRepository;
    }

    public function __invoke(
        int $userId, ?string $address = null, ?string $phoneNumber = null
    ): JsonResponse {
        return $this->clientRepository->modifyClient(['id' => $userId], $address, $phoneNumber);
    }
}